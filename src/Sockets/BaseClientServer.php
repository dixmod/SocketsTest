<?php

namespace Dixmod\Sockets;

abstract class BaseClientServer implements InterfaceClientServer
{
    /** @var string */
    protected $address = '127.0.0.1';

    /** @var int */
    protected $port = 1234;

    /** @var array */
    protected $runRequiredParams = [
        'address::',
        'port::'
    ];

    protected const MESSAGE_LIMIT = 2048;

    /** @var resource */
    protected $socket;

    protected $runParams = [];

    public function __construct()
    {
        $this->runParams = getopt('', $this->getRunRequiredParams());

        if (!empty($this->runParams['address'])) {
            $this->setAddress($this->runParams['address']);
        }

        if (!empty($this->runParams['port'])) {
            $this->setPort($this->runParams['port']);
        }
    }

    protected function createSocket()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false) {
            throw new Exceptions\Socket('Socket created failed');
        }
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return array
     */
    public function getRunRequiredParams(): array
    {
        return $this->runRequiredParams;
    }

    /**
     * @param array $runRequiredParams
     */
    public function setRunRequiredParams(array $runRequiredParams): void
    {
        $this->runRequiredParams = $runRequiredParams;
    }

    public function __destruct()
    {
        socket_close($this->socket);
    }
}