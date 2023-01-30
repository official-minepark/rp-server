<?php

namespace minepark\application\servercommands\info;

use minepark\application\servercommands\ServerCommand;
use minepark\common\client\ClientResponse;
use minepark\domain\responses\GetClientInfoQueryResponse;
use minepark\infrastructure\dataservices\ClientsDataService;
use minepark\plugin\MainPlugin;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class VersionServerCommand extends ServerCommand
{
    private const COMMAND_NAME = "version";

    public function __construct(
        private ClientsDataService $clientsDataService,
        private MainPlugin $mainPlugin
    )
    {
        parent::__construct();
    }

    public function getCommandName(): array
    {
        return [
            self::COMMAND_NAME
        ];
    }

    public function getCommandDescription(): string
    {
        return TextFormat::YELLOW . "Получение информации о сервере";
    }

    public function executeCommand(CommandSender $sender, string $commandLabel, array $args): \Generator
    {
        /**
         * @var ClientResponse<GetClientInfoQueryResponse> $clientInfo
         */
        $clientInfo = yield from $this->clientsDataService->getInfo();

        $sender->sendMessage(TextFormat::AQUA . "В данный момент MinePark находится на версии " . TextFormat::YELLOW . $this->mainPlugin->getDescription()->getVersion());
        $sender->sendMessage(TextFormat::AQUA . "Проект " . TextFormat::YELLOW . "MinePark" . TextFormat::AQUA . " в данный момент подключен к " . TextFormat::YELLOW . $clientInfo->getBody()->productName . TextFormat::AQUA . " версии " . TextFormat::YELLOW . $clientInfo->getBody()->version);
    }
}