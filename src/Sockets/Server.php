<?php

namespace Dixmod\Sockets;

class Server extends BaseClientServer
{
    /** @var array */
    protected $runRequiredParams = [
        'address::',
        'port::',
        'threads::'
    ];

    /** @var int */
    protected $threads = 1;

    public function __construct()
    {
        parent::__construct();

        $this->threads = $params['threads'] ?? 1;

        $this->createServer();
    }

    public function run(): void
    {
        for ($i = 0; $i < $this->getThreads(); $i++) {

            $pid_fork = pcntl_fork();

            // child process
            if ($pid_fork == 0) {

                while (true) {
                    $pid = posix_getpid();
                    $socket = socket_accept($this->socket);

                    echo '[' . $pid_fork . '] Acceptor connect: ' . $socket . PHP_EOL;
                    socket_write($socket, 'Process pid: ' . $pid . PHP_EOL);

                    $command = trim(socket_read($socket, 2048));
                    echo 'Retrieve command: ' . $command . PHP_EOL;

                    socket_write($socket, '[' . $command . ']' . PHP_EOL);

                    socket_close($socket);
                }
            }
        }

        while (($cid = pcntl_waitpid(0, $status)) != -1) {
            $exit_code = pcntl_wexitstatus($status);
            echo '[' . $cid . '] exited with status: ' . $exit_code . PHP_EOL;
        }
    }

    /**
     * @return int
     */
    private function getThreads(): int
    {
        return $this->threads;
    }

    private function createServer()
    {
        $this->createSocket();

        socket_set_option(
            $this->socket,
            SOL_SOCKET,
            SO_REUSEADDR,
            1
        );

        if (!socket_bind($this->socket, $this->getAddress(), $this->getPort())) {
            die('Socket bind failed: ' . socket_strerror(socket_last_error()) . PHP_EOL);
        }

        if (!socket_listen($this->socket, 1)) {
            die('Socket listen failed: ' . socket_strerror(socket_last_error()) . PHP_EOL);
        }
    }
}