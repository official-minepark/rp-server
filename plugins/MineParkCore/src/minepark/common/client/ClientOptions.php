<?php

namespace minepark\common\client;

class ClientOptions
{
    public function __construct(
        private string $ip,
        private int    $port,
        private string $token
    )
    {
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}