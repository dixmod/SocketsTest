<?php

namespace Dixmod\Sockets;

use Dixmod\Sockets\Exceptions;

class Client extends BaseClientServer
{
    /** @var array */
    protected $runRequiredParams = [
        'address::',
        'port::',
        'message:'
    ];

    protected $message = '';

    public function __construct()
    {
        parent::__construct();

        if (!empty($this->runParams['message'])) {
            $this->setMessage($this->runParams['message']);
        }

        $this->connect();
    }

    public function run(): void
    {

        socket_write(
            $this->socket,
            $this->message,
            strlen($this->message)
        );

        $answer = '';
        while (($line = socket_read($this->socket, self::MESSAGE_LIMIT)) !== '') {
            $answer .= $line;
        }

        echo $answer . PHP_EOL;

        $this->setMessage(substr($answer, 0, strlen($answer) - 1));

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
             throw new Exceptions\Socket('Socket connect failed');
        }
    }
}