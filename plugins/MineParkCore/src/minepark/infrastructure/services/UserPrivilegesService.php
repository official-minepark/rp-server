<?php

namespace minepark\infrastructure\services;

use minepark\common\client\ClientResponse;
use minepark\domain\models\UserPrivilege;
use minepark\infrastructure\dataservices\UserPrivilegesDataService;
use minepark\infrastructure\models\UserStatesMapModel;
use minepark\plugin\MainPlugin;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class UserPrivilegesService extends BaseService
{
    public function __construct(
        private UserPrivilegesDataService $userPrivilegesDataService,
        private MainPlugin $mainPlugin
    )
    {}

    public function initializeUser(Player $user, UserStatesMapModel $statesMap): \Generator
    {
        $privilegeName = $statesMap->profile->privilege;

        /**
         * @var ClientResponse<UserPrivilege> $response
         */
        $response = yield from $this->userPrivilegesDataService->getByName($privilegeName);

        $statesMap->privilege = $response->getBody();

        /**
         * @var ClientResponse<array<string>> $response
         */
        $response = yield from $this->userPrivilegesDataService->calculatePermissions($privilegeName);

        $this->addPermissions($user, $response->getBody(), $statesMap);

        $this->sendMessage($user, TextFormat::AQUA . "Ваша привилегия - " . TextFormat::YELLOW . $statesMap->privilege->displayName);
    }

    private function addPermissions(Player $user, array $permissions, UserStatesMapModel $statesMap): void
    {
        foreach ($permissions as $permission) {
            $statesMap->permissions[] = $user->addAttachment($this->mainPlugin, $permission, true);
        }
    }

    private function sendMessage(Player $player, string $message): void
    {
        $player->sendMessage(TextFormat::BLUE . TextFormat::BOLD . " Привилегии » " . TextFormat::RESET . $message);
    }
}