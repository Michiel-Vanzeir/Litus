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

namespace CommonBundle\Component\Console\Command;

use CommonBundle\Component\React\EventLoop\LoopAwareInterface;
use CommonBundle\Component\React\Socket\Server;
use Closure;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory as EventLoopFactory;

abstract class Socket extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $module = $this->getModuleName();
        $name = strtolower($this->getCommandName());

        $this->setName('socket:' . $module . ':' . $name)
            ->setDescription('Starts the ' . $name . ' socket');
    }

    protected function invoke()
    {
        if (!$this->isSocketEnabled()) {
            $this->writeln('<info>The ' . $this->getCommandName() . ' socket is not enabled</info>');
            return;
        }

        $this->writeln('Starting the <comment>' . $this->getCommandName() . '</comment> socket...');

        $loop = EventLoopFactory::create();

        $socket = $this->getSocket();
        if ($socket instanceof LoopAwareInterface) {
            $socket->setLoop($loop);
        }

        $server = new IoServer(
            new HttpServer(
                new WsServer(
                    $socket
                )
            ),
            Server::factory($this->getSocketUri(), $loop),
            $loop
        );
        $server->run();
    }

    /**
     * @return string The name of this WebSocket, used in the commands
     */
    abstract protected function getCommandName();

    /**
     * @return \Ratchet\MessageComponentInterface The application that I/O will call when events are received
     */
    abstract protected function getSocket();

    /**
     * @return string
     */
    abstract protected function getSocketUri();

    /**
     * @return boolean Whether the socket is enabled
     */
    abstract protected function isSocketEnabled();

    /**
     * @return string The name of the bundle of this WebSocket
     */
    private function getModuleName()
    {
        return str_replace(
            array(
                'bundle',
                'module'
            ),
            '',
            strtolower(
                substr(static::class, 0, strpos(static::class, '\\', 1))
            )
        );
    }
}
