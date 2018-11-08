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

namespace CommonBundle\Component\WebSocket;

use RuntimeException;

/**
 * This is the server to handle all requests by the WebSocket protocol.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
abstract class Server
{
    private $master;

    private $users;
    private $sockets;
    private $authenticated;

    private $command;

    const OP_CONT = 0x0;
    const OP_TEXT = 0x1;
    const OP_BIN = 0x2;
    const OP_CLOSE = 0x8;
    const OP_PING = 0x9;
    const OP_PONG = 0xa;

    public function __construct($file)
    {
        $this->master = null;

        $this->users = array();
        $this->sockets = array();
        $this->authenticated = array();

        $this->registerSignalHandlers();
        $this->createSocket($file);
    }

    /**
     * Register process signal handlers
     */
    private function registerSignalHandlers()
    {
        pcntl_async_signals(true);

        pcntl_signal(SIGTERM, function () {
            $this->destroySocket();
        });
    }

    /**
     * Create the master socket
     */
    private function createSocket($file)
    {
        $err = $errno = 0;

        $isFile = strpos($file, 'unix://') === 0;
        $fileName = substr($file, strlen('unix://'));

        if ($isFile) {
            if (file_exists($fileName)) {
                unlink($fileName);
            }

            if (!file_exists(dirname($fileName))) {
                mkdir(dirname($fileName));
            }
        }

        $this->master = stream_socket_server($file, $errno, $err, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN);

        if ($isFile) {
            chmod($fileName, 0777);
        }

        $this->sockets[] = $this->master;

        if ($this->master === false) {
            throw new RuntimeException('Socket could not be created: ' . $err);
        }
    }

    private function destroySocket()
    {
        foreach ($this->sockets as $socket) {
            if ($socket == $this->master) {
                continue;
            }

            $this->removeUserSocket($socket);
        }

        if (!is_resource($this->master)) {
            return;
        }

        fclose($this->master);
        $this->master = null;
    }

    /**
     * Start listening on master socket and user sockets
     */
    public function process()
    {
        do {
            clearstatcache();
            gc_collect_cycles();

            $read = $this->sockets;
            $write = array();
            $except = array();
            @stream_select($read, $write, $except, null);

            foreach ($read as $socket) {
                if (!is_resource($socket)) {
                    continue;
                }

                if ($socket == $this->master) {
                    $this->addUserSocket(stream_socket_accept($this->master));
                } else {
                    $buffer = fread($socket, 2048);

                    if ($buffer === false || strlen($buffer) === 0) {
                        $this->removeUserSocket($socket);
                    } else {
                        $user = $this->getUserBySocket($socket);
                        if ($user->hasHandshaked()) {
                            $this->processFrame($user, $buffer);
                        } else {
                            $user->doHandShake($buffer);
                            if ($user->hasHandshaked()) {
                                $this->onConnect($user);
                            }
                        }
                    }
                }
            }
        } while ($this->master !== null);
    }

    /**
     * Add a user socket to listen to
     *
     * @param  resource $socket
     * @return null
     */
    private function addUserSocket($socket)
    {
        if ($socket === false) {
            return;
        }

        $this->users[] = new User($socket);
        $this->sockets[] = $socket;
    }

    /**
     * Add a authenticated socket
     *
     * @param  resource $socket
     * @return null
     */
    protected function addAuthenticated($socket)
    {
        $this->authenticated[(int) $socket] = $socket;
    }

    /**
     * Check a authenticated socket
     *
     * @param  resource $socket
     * @return boolean
     */
    protected function isAuthenticated($socket)
    {
        return isset($this->authenticated[(int) $socket]);
    }

    /**
     * Get a user by his socket
     *
     * @param  resource $socket
     * @return User
     */
    public function getUserBySocket($socket)
    {
        foreach ($this->users as $user) {
            if ($user->getSocket() == $socket) {
                return $user;
            }
        }
    }

    /**
     * Remove a user socket
     *
     * @param  resource $socket
     * @return null
     */
    private function removeUserSocket($socket)
    {
        if (!is_resource($socket)) {
            return;
        }

        foreach ($this->users as $key => $value) {
            if ($value->getSocket() == $socket) {
                unset($this->users[$key]);
                $this->onClose($value, 0, '');
            }
        }

        if (isset($this->authenticated[(int) $socket])) {
            unset($this->authenticated[(int) $socket]);
        }

        foreach ($this->sockets as $key => $value) {
            if ($value == $socket) {
                unset($this->sockets[$key]);
            }
        }

        fclose($socket);
    }

    /**
     * Remove a user
     *
     * @param  User $user
     * @return null
     */
    protected function removeUser(User $user)
    {
        $this->removeUserSocket($user->getSocket());
    }

    /**
     * Process a frame send by a user to the master socket
     *
     * @param  User   $user
     * @param  string $data
     * @return null
     */
    private function processFrame(User $user, $data)
    {
        $f = new Frame($data);

        if ($f->getIsFin() && $f->getOpcode() != 0) {
            if ($f->getIsControl()) {
                $this->handleControlFrame($user, $f);
            } else {
                $this->handleDataFrame($user, $f);
            }
        } elseif (!$f->getIsFin() && $f->getOpcode() != 0) {
            $user->createBuffer($f);
        } elseif (!$f->getIsFin() && $f->getOpcode() == 0) {
            $user->appendBuffer($f);
        } elseif ($f->getIsFin() && $f->getOpcode() == 0) {
            $user->appendBuffer($f);

            $buffer = $user->getBuffer();
            if ($buffer !== null) {
                $this->handleDataFrame($user, $buffer);
            }

            $user->clearBuffer();
        }
    }

    /**
     * Handle the received control frames
     *
     * @param  User  $user
     * @param  Frame $frame
     * @return null
     */
    private function handleControlFrame(User $user, Frame $frame)
    {
        $len = strlen($frame->getData());

        if ($frame->getOpcode() == self::OP_CLOSE) {
            if ($len !== 0 && $len === 1) {
                return;
            }

            $statusCode = false;
            $reason = '';

            if ($len >= 2) {
                $unpacked = unpack('n', substr($frame->getData(), 0, 2));
                $statusCode = $unpacked[1];
                $reason = substr($frame->getData(), 3);
            }

            $user->write(chr(0x88) . chr(0));

            $this->removeUserSocket($user->getSocket());
            $this->onClose($user, $statusCode, $reason);
        }
    }

    /**
     * Handle a received data frame
     *
     * @param  User  $user
     * @param  Frame $frame
     * @return null
     */
    protected function handleDataFrame(User $user, Frame $frame)
    {
        if ($frame->getOpcode() == self::OP_TEXT) {
            $this->gotText($user, $frame->getData());
        } elseif ($frame->getOpcode() == self::OP_BIN) {
            $this->gotBin($user, $frame->getData());
        }
    }

    /**
     * Send text to a user socket
     *
     * @param  User   $user
     * @param  string $text
     * @return null
     */
    public function sendText($user, $text)
    {
        if (!$this->isAuthenticated($user->getSocket())) {
            return;
        }

        $len = strlen($text);

        if ($len > 0xffff) {
            return;
        }

        $header = chr(0x81);

        if ($len >= 126) {
            $header .= chr(126) . pack('n', $len);
        } else {
            $header .= chr($len);
        }

        $user->write($header . $text);
    }

    /**
     * Send text to all user socket
     *
     * @param  string $text
     * @return null
     */
    public function sendTextToAll($text)
    {
        $len = strlen($text);

        if ($len > 0xffff) {
            return;
        }

        $header = chr(0x81);

        if ($len >= 126) {
            $header .= chr(126) . pack('n', $len);
        } else {
            $header .= chr($len);
        }

        foreach ($this->users as $user) {
            if ($this->isAuthenticated($user->getSocket())) {
                $user->write($header . $text);
            }
        }
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Parse received text
     *
     * @param  User   $user
     * @param  string $data
     * @return null
     */
    abstract protected function gotText(User $user, $data);

    /**
     * Parse received binary
     *
     * @param  User   $user
     * @param  string $data
     * @return null
     */
    abstract protected function gotBin(User $user, $data);

    /**
     * Do action when user closed his socket
     *
     * @param  User    $user
     * @param  integer $statusCode
     * @param  string  $reason
     * @return null
     */
    protected function onClose(User $user, $statusCode, $reason)
    {
        $this->removeUserSocket($user->getSocket());
    }

    /**
     * Do action when a new user has connected to this socket
     *
     * @param  User $user
     * @return null
     */
    abstract protected function onConnect(User $user);
}
