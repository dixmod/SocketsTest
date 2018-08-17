<?php

namespace Dixmod\Sockets;

class Client extends BaseClientServer
{
    /** @var array */
    protected $runRequiredParams = [
        'address::',
        'port::',
        'message:'
    ];

    protected $message = 'GET /';

    public function __construct()
    {
        parent::__construct();

        if (!empty($this->runParams['message'])) {
            $this->setMessage($this->runParams['message']);
        }

        $this->connect();
        print_r($this->message); exit;
    }

    public function run(): void
    {
        socket_write(
            $this->socket,
            $this->message,
            strlen($this->message)
        );

        $answer = '';
        while (($line = socket_read($this->socket, 2048)) !== '') {
            $answer .= $line;
        }

        echo $answer . PHP_EOL;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message . PHP_EOL;
    }

    private function connect()
    {
        $this->createSocket();

        $connect = socket_connect($this->socket, $this->getAddress(), $this->getPort());
        if ($connect === false) {
            die('Socket connect failed: ' . socket_strerror(socket_last_error()) . PHP_EOL);
        }
    }
}