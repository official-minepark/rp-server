<?php

namespace minepark\infrastructure\services;

use Generator;
use minepark\application\views\responses\UserStartViewResponse;
use minepark\application\views\UserStartView;
use minepark\common\client\ClientResponse;
use minepark\domain\constants\ServerConstants;
use minepark\domain\models\User;
use minepark\infrastructure\dataservices\UsersDataService;
use minepark\infrastructure\events\UserInitializeEvent;
use minepark\infrastructure\events\UserQuitEvent;
use minepark\infrastructure\models\UserStatesMapModel;
use minepark\infrastructure\stores\UserStatesMapStore;
use minepark\plugin\MainPlugin;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SOFe\AwaitGenerator\Await;
use SOFe\AwaitGenerator\GeneratorUtil;
use SOFe\AwaitGenerator\Mutex;
use SOFe\AwaitStd\AwaitStd;

class UsersService extends BaseService
{
    private const SERVICE_NAME = "Пользователи";

    public function __construct(
        private MainPlugin $mainPlugin,
        private UsersDataService $usersDataService,
        private UserStartView $userStartView,
        private UserStatesMapStore $userStatesMapStore
    )
    {
    }

    public function initializeUser(Player $user): Generator
    {
        if (!$this->mainPlugin->isInitialized()) {
            $user->kick("Сервер еще не инициализирован. Попробуйте зайти позже");
            return;
        }

        $user->setImmobile();
        $user->setDisplayName("");

        $mutex = new Mutex;

        $this->userStatesMapStore->setUser($user, $mutex);

        yield from $mutex->runClosure(function() use($user): Generator {
            $statesMap = new UserStatesMapModel;

            $statesMap->isNew = false;
            $statesMap->user = $user;

            /**
             * @var ClientResponse<User> $response
             */
            $response = yield from $this->usersDataService->getByXuid($user->getXuid() === "" ? $user->getUniqueId()->serialize() : $user->getXuid());

            if (!$response->isSuccess()) {
                $response = yield from $this->tryCreatingUser($user);
                $statesMap->isNew = true;
            }

            $statesMap->profile = $response->getBody();

            $event = new UserInitializeEvent($user, $statesMap);
            $event->call();

            yield from $event->awaitForFinish();

            $this->userStatesMapStore->setUser($user, $statesMap);

            yield from $this->onPostInitialize($user);
        });
    }

    public function onUserQuit(Player $user): Generator
    {
        $userInfo = yield from $this->userStatesMapStore->getUserAsync($user);

        $event = new UserQuitEvent($user, $userInfo);
        $event->call();

        yield from $event->awaitForFinish();

        $this->userStatesMapStore->removeUser($user);
        $this->mainPlugin->getServer()->broadcastMessage(TextFormat::DARK_RED . $userInfo->profile->name . TextFormat::GRAY . " вышел из сервера");
    }

    public function changeUserEmail(Player $user, string $newEmail): Generator
    {
        $userInfo = $this->userStatesMapStore->getUser($user);

        if (is_null($userInfo)) {
            throw new \RuntimeException("Attempt to change email for uninitialized user");
        }

        $userId = $userInfo->profile->id;

        /**
         * @var ClientResponse $response
         */
        $response = yield from $this->usersDataService->changeEmail($userId, $newEmail);

        if ($response->isSuccess()) {
            $userInfo->profile->email = $newEmail;
            $this->sendMessage($user, TextFormat::GREEN . "Ваша почта успешно сменена на " . TextFormat::AQUA . $newEmail . TextFormat::GREEN . "!");
        }

        return $response;
    }

    public function changeUserPrivilege(int $userId, string $privilege): Generator
    {
        /**
         * @var ClientResponse<User> $response
         */
        $response = yield from $this->usersDataService->getById($userId);

        if (!$response->isSuccess()) {
            return false;
        }

        $userInfo = $response->getBody();

        if ($userInfo->privilege === $privilege) {
            return false;
        }

        /**
         * @var ClientResponse $response
         */
        $response = yield from $this->usersDataService->setPrivilege($userId, $privilege);

        if (!$response->isSuccess()) {
            return false;
        }

        $userData = $this->userStatesMapStore->findById($userId);

        if ($userData === null) {
            return true;
        }

        $this->sendMessage($userData->user, TextFormat::AQUA . "Ваша привилегия изменена на '" . TextFormat::YELLOW . $privilege . TextFormat::AQUA . "'");
        $this->sendMessage($userData->user, TextFormat::AQUA . "Для того, чтобы изменения применились, перезайдите в игру");

        return true;
    }

    private function tryCreatingUser(Player $player): Generator
    {
        /**
         * @var UserStartViewResponse $createMenuResponse
         */
        $createMenuResponse = yield from $this->userStartView->sendToPlayer($player);

        /**
         * @var ClientResponse<User> $response
         */
        $response = yield from $this->usersDataService->create($createMenuResponse->getName(), $player->getXuid() === "" ? $player->getUniqueId()->serialize() : $player->getXuid(), $createMenuResponse->getEmail());

        if (!$response->isSuccess()) {
            return yield from $this->tryCreatingUser($player);
        }

        return $response;
    }

    private function onPostInitialize(Player $user): Generator
    {
        if (!$user->isOnline()) {
            return yield from GeneratorUtil::empty();
        }

        $userData = $this->userStatesMapStore->getUser($user);

        $userId = $userData->profile->id;
        $userName = $userData->profile->name;
        $isNew = $userData->isNew;

        $user->sendTitle(TextFormat::YELLOW . "Сервер " . ServerConstants::SERVER_NAME, TextFormat::YELLOW . "Добро пожаловать, " . TextFormat::AQUA . $userName . "!");
        $user->setImmobile(false);
        $user->setDisplayName($userName);
        $user->setNameTag($userName);

        $this->sendMessage($user, TextFormat::AQUA . "Добро пожаловать на сервер " . ServerConstants::SERVER_NAME);
        $this->sendMessage($user, TextFormat::AQUA . "Мы рады вас видеть, " . TextFormat::YELLOW . $userName . TextFormat::AQUA . "!");
        $this->sendMessage($user, TextFormat::AQUA . "Наша группа ВКонтакте: " . TextFormat::YELLOW . ServerConstants::VK_PAGE);
        $this->sendMessage($user, TextFormat::AQUA . "Наш сайт: " . TextFormat::YELLOW . ServerConstants::SERVER_WEBSITE);

        if ($isNew) {
            $this->sendMessage($user, TextFormat::AQUA . "Судя по всему, вы здесь " . TextFormat::YELLOW . "новый");
        }

        $this->mainPlugin->getServer()->broadcastMessage(TextFormat::DARK_RED . $userName . TextFormat::GRAY . " зашел на сервер");
        $this->mainPlugin->getLogger()->notice("У пользователя " . $userName . "(IGN: " . $user->getName() . ") ID - $userId");

        return yield from GeneratorUtil::empty();
    }

    private function sendMessage(Player $user, string $message)
    {
        // « » ‹ ›
        $user->sendMessage(TextFormat::GOLD . TextFormat::BOLD . " Пользователи » " . TextFormat::RESET . $message);
    }
}