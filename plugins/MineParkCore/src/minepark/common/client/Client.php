<?php

namespace minepark\common\client;

use Generator;
use minepark\common\client\tasks\AsyncHttpRequestTask;
use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;
use minepark\common\mapping\Mapper;
use pocketmine\Server;
use SOFe\AwaitGenerator\Await;

class Client implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;

    private ClientOptions $clientOptions;

    public function __construct(
        private Server $server,
        private Mapper $mapper
    )
    {
    }

    /**
     * Warning, has slash in the end
     * @return string
     */
    public function generateUrl(): string
    {
        return "https://" . $this->getIp() . ":" . $this->getPort() . "/";
    }

    public function getIp(): string
    {
        return $this->clientOptions->getIp();
    }

    public function getPort(): int
    {
        return $this->clientOptions->getPort();
    }

    public function sendRequestAsync(ClientRequest $request): Generator
    {
        $request->addHeaders($this->getDefaultHeaders());

        return yield from Await::promise(function ($resolve, $reject) use ($request) {
            $task = new AsyncHttpRequestTask($this, $request, function (int $code, mixed $body) use ($request, $resolve) {
                $response = $this->processRequest($request, $code, $body);
                $resolve($response);
            });

            $this->server->getAsyncPool()->submitTask($task);
        });
    }

    private function getDefaultHeaders(): array
    {
        return [
            new RequestHeader("Authentication", $this->getToken()),
            new RequestHeader("Content-Type", "application/json"),
            new RequestHeader("Accept", "application/json")
        ];
    }

    public function getToken(): string
    {
        return $this->clientOptions->getToken();
    }

    private function processRequest(ClientRequest $request, int $code, mixed $body): ClientResponse
    {
        $body = json_decode(json_encode(json_decode($body)), true);

        if (str_starts_with($code, "2")) {
            return new ClientResponse($request, $code, $body, null);
        }

        if ($code === 400) {
            return new ClientResponse($request, $code, null, $this->mapper->map($body, ApplicationGenericError::class));
        }

        return new ClientResponse($request, $code, null, null);
    }

    public function setOptions(ClientOptions $clientOptions): void
    {
        $this->clientOptions = $clientOptions;
    }
}