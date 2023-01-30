<?php

namespace minepark\common\mdc;

use Generator;
use minepark\common\client\Client;
use minepark\common\client\ClientOptions;
use minepark\common\client\ClientResponse;
use minepark\common\configuration\ConfigurationManager;
use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;
use minepark\common\utils\ServerInitializationQueue;
use minepark\domain\constants\ServerConstants;
use minepark\domain\responses\GetClientInfoQueryResponse;
use minepark\infrastructure\dataservices\ClientsDataService;
use minepark\plugin\MainPlugin;
use SOFe\AwaitGenerator\Await;
use SOFe\AwaitStd\AwaitStd;

class MDC implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;

    public function __construct(
        private Client             $client,
        private MainPlugin         $plugin,
        private ClientsDataService $clientsDataService,
        private AwaitStd $awaitStd
    )
    {
        $configurationManager = new ConfigurationManager(
            filePath: $this->plugin->getDataFolder() . "mdc.yml",
            dataStructure: [
                "ip" => "string",
                "port" => "int",
                "token" => "string"
            ],
            defaults: [
                "ip" => "127.0.0.1",
                "port" => 20000,
                "token" => "aaaa-bbbb-ccc"
            ]
        );

        Await::g2c(ServerInitializationQueue::getMutex()->run(
            $this->initializeClient(
                $this->client,
                $configurationManager->getEntry("ip"),
                $configurationManager->getEntry("port"),
                $configurationManager->getEntry("token")
            )
        ));
    }

    private function initializeClient(Client $client, string $ip, int $port, string $token): Generator
    {
        $client->setOptions(
            new ClientOptions(
                ip: $ip,
                port: $port,
                token: $token
            )
        );

        /**
         * @var ClientResponse<GetClientInfoQueryResponse> $response
         */
        $response = yield from $this->clientsDataService->getInfo();

        if (!$response->isSuccess()) {
            if ($response->isTimeout()) {
                $this->plugin->getLogger()->error("MDC is not running. Disabling server...");
            } else if ($response->getError() !== null) {
                $this->plugin->getLogger()->error("Can't connect to mdc by reason: " . "[" . $response->getError()->serviceName . "] " . $response->getError()->message);
            } else {
                $this->plugin->getLogger()->error("Can't connect to MDC by unknown reasons. Response code " . $response->getCode());
            }

            yield from $this->shutdownServer();
            return;
        }

        $clientInfo = $response->getBody();

        if ($clientInfo->protocolVersion !== ServerConstants::PROTOCOL_VERSION) {
            $this->plugin->getLogger()->error("Server protocol version is " . ServerConstants::PROTOCOL_VERSION . ", while " . $clientInfo->productName . " uses " . $clientInfo->protocolVersion);
            yield from $this->shutdownServer();
            return;
        }

        $this->plugin->getLogger()->notice("Using " . $clientInfo->productName . " version " . $clientInfo->version . " as DataCenter. Current client name - " . $clientInfo->name);
    }

    private function shutdownServer(): Generator
    {
        yield from $this->awaitStd->sleep(60);
        $this->plugin->getServer()->shutdown();
    }
}