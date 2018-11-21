<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Component\Socket;

use Clue\React\Redis\Client as RedisClient;
use Clue\React\Redis\Factory as RedisFactory;
use CommonBundle\Component\Acl\Acl;
use CommonBundle\Component\Console\Command;
use CommonBundle\Component\Ratchet\Redis\MessageComponentInterface as RedisMessageComponentInterface;
use CommonBundle\Component\React\EventLoop\LoopAwareInterface;
use CommonBundle\Component\React\EventLoop\LoopAwareTrait;
use CommonBundle\Component\Redis\Uri as RedisUri;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\RedisClientTrait;
use Exception;
use Ko\Process;
use Ko\ProcessManager;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;
use SyllabusBundle\Component\Parser\Study as StudyParser;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * This is the server to handle all requests by the WebSocket protocol
 * for the Queue.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Update extends \CommonBundle\Component\Socket\Server implements LoopAwareInterface, RedisMessageComponentInterface
{
    use RedisClientTrait;

    use LoopAwareTrait {
        setLoop as traitSetLoop;
    }

    /**
     * @var array
     */
    const REDIS_CHANNELS = array(
        StudyParser::class => 'syllabus_parser_study'
    );

    /**
     * @var ProcessManager
     */
    private $processManager;

    /**
     * @var RedisClient
     */
    private $redisClient;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param Command                 $command
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, Command $command)
    {
        parent::__construct($serviceLocator, $command);

        $this->processManager = new ProcessManager();
    }

    /**
     * @param  LoopInterface $loop
     * @return self
     */
    public function setLoop(LoopInterface $loop)
    {
        $this->traitSetLoop($loop);
        $this->createRedisClient();

        return $this;
    }

    private function createRedisClient()
    {
        $this->redisClient = null;

        $redisFactory = new RedisFactory($this->getLoop());
        $redisUri = RedisUri::build($this->getConfig()['redis']);

        $redisFactory->createClient($redisUri)->then(
            function (RedisClient $client) {
                $client->on('close', array($this, 'onRedisClose'));
                $client->on('error', array($this, 'onRedisError'));

                $client->on('message', array($this, 'onRedisMessage'));

                $this->redisClient = $client;
            },
            function (Exception $e) {
                $this->onRedisError($e);
            }
        );
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        parent::onOpen($conn);

        if ($this->redisClient === null) {
            $conn->close();
        }
    }

    /**
     * @param ConnectionInterface $from
     * @param string              $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $this->getEntityManager()->getConnection()->close();
        $this->getEntityManager()->clear();

        $command = json_decode($msg);
        if ($command === null) {
            $this->writeln('<comment>Failed to decode received message</comment>');
            return;
        }

        $user = $this->getUser($from);
        if ($user === null) {
            $this->writeln('<info>Received message for unrecognized connection</info>');
            return;
        }

        $key = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.update_socket_key');

        if (!isset($command->key) || $command->key != $key) {
            $this->writeln('<error>Connection with invalid key</error>');
            $this->removeUser($user);

            return;
        }

        if (!isset($command->authSession) || !$user->authenticate($command->authSession)) {
            $this->writeln('<error>Failed to authenticate user</error>');
            $this->removeUser($user);

            return;
        }

        if (!$user->isAllowed('syllabus_admin_update', 'updateNow')) {
            $this->writeln('<error>Unauthorized connection</error>');
            $this->removeUser($user);

            return;
        }

        switch ($command->command) {
            case 'update':
                if ($this->processManager->hasAlive()) {
                    return;
                }

                $this->redisClient->subscribe(
                    self::REDIS_CHANNELS[StudyParser::class]
                );

                $this->processManager->fork(
                    function (Process $p) {
                        $p->setProcessTitle('litus:syllabus:study-parser');

                        // Close parent connection to force reconnection in child process
                        $this->getEntityManager()->getConnection()->close();

                        $studyParser = new StudyParser(
                            $this->getEntityManager(),
                            $this->getMailTransport(),
                            $this->getRedisClient()
                        );
                        $studyParser->update();
                    }
                );

                break;

            default:
                $this->writeln('Received invalid command <comment>' . $command->command . '</comment>');
        }
    }

    public function onRedisClose()
    {
        $this->createRedisClient();
    }

    /**
     * @param Exception $e
     */
    public function onRedisError(Exception $e)
    {
        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getSentryClient()->logException($e);
        }

        $this->writeln('<error>' . $e->getMessage() . '</error>');
    }

    /**
     * @param string $channel
     * @param string $payload
     */
    public function onRedisMessage($channel, $payload)
    {
        switch ($channel) {
            case self::REDIS_CHANNELS[StudyParser::class]:
                $payload = $this->getRedisClient()->_unserialize($payload);
                if ($payload['type'] == 'done') {
                    $this->redisClient->unsubscribe();
                }

                foreach ($this->getUsers() as $user) {
                    $user->send(
                        json_encode(
                            (object) array(
                                'status' => (object) $payload
                            )
                        )
                    );
                }

                break;

            default:
                $this->writeln('Received message on invalid Redis channel <comment>' . $channel . '</comment>');
        }
    }
}
