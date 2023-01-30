<?php

namespace minepark\infrastructure\dataservices;

use minepark\common\client\Client;
use minepark\common\client\ClientResponse;
use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;
use minepark\common\mapping\Mapper;

abstract class DataService implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;

    public function __construct(
        protected Client $client,
        protected Mapper $mapper
    )
    {
    }

    public function tryMappingResponse(ClientResponse $response, string $mapTo): void
    {
        if ($response->isSuccess()) {
            $response->setBody($this->mapper->map($response->getBody(), $mapTo));
        }
    }
}