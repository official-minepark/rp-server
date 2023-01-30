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

    public function initializeUser(Player $player): Generator
    {
        if (!$this->mainPlugin->isInitialized()) {
            $player->kick("Сервер еще не инициализирован. Попробуйте зайти позже");
            return;
        }

        $player->setImmobile();
        $player->setDisplayName("");

        $statesMap = new UserStatesMapModel;

        $statesMap->isNew = false;
        $statesMap->user = $player;

        /**
         * @var ClientResponse<User> $response
         */
        $response = yield from $this->usersDataService->getByXuid($player->getXuid());

        if (!$response->isSuccess()) {
            $response = yield from $this->tryCreatingUser($player);
            $statesMap->isNew = true;
        }

        $statesMap->profile = $response->getBody();

        $initializationMutex = new Mutex;

        $event = new UserInitializeEvent($player, $statesMap, $initializationMutex);
        $event->call();

        Await::g2c($initializationMutex->runClosure(function() use($player, $statesMap) : Generator {
            $this->userStatesMapStore->setForUser($player, $statesMap);
            return GeneratorUtil::empty();
        }));

        Await::g2c($initializationMutex->run($this->onPostInitialize($player)));
    }

    public function onUserQuit(Player $user): Generator
    {
        $userInfo = $this->userStatesMapStore->getForUser($user);

        if (is_null($userInfo)) {
            return GeneratorUtil::empty();
        }

        $mutex = new Mutex();

        $event = new UserQuitEvent($user, $userInfo, $mutex);
        $event->call();

        Await::g2c($mutex->runClosure(function() use($user, $userInfo) : Generator {
            $this->userStatesMapStore->removeForUser($user);
            $this->mainPlugin->getServer()->broadcastMessage(TextFormat::DARK_RED . $userInfo->profile->name . TextFormat::GRAY . " вышел из сервера");
            yield from GeneratorUtil::empty();
        }));

        return GeneratorUtil::empty();
    }

    public function changeUserEmail(Player $player, string $newEmail): Generator
    {
        $userInfo = $this->userStatesMapStore->getForUser($player);

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
            $this->sendMessage($player, TextFormat::GREEN . "Ваша почта успешно сменена на " . TextFormat::AQUA . $newEmail . TextFormat::GREEN . "!");
        }

        return $response;
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
        $response = yield from $this->usersDataService->create($createMenuResponse->getName(), $player->getXuid(), $createMenuResponse->getEmail());

        if (!$response->isSuccess()) {
            return yield from $this->tryCreatingUser($player);
        }

        return $response;
    }

    private function onPostInitialize(Player $player): Generator
    {
        $userName = $this->userStatesMapStore->getForUser($player)->profile->name;
        $isNew = $this->userStatesMapStore->getForUser($player)->isNew;

        $player->sendTitle(TextFormat::YELLOW . "Сервер " . ServerConstants::SERVER_NAME, TextFormat::YELLOW . "Добро пожаловать, " . TextFormat::AQUA . $userName . "!");
        $player->setImmobile(false);
        $player->setDisplayName($userName);
        $player->setNameTag($userName);

        $this->sendMessage($player, TextFormat::AQUA . "Добро пожаловать на сервер " . ServerConstants::SERVER_NAME);
        $this->sendMessage($player, TextFormat::AQUA . "Мы рады вас видеть, " . TextFormat::YELLOW . $userName . TextFormat::AQUA . "!");
        $this->sendMessage($player, TextFormat::AQUA . "Наша группа ВКонтакте: " . TextFormat::YELLOW . ServerConstants::VK_PAGE);
        $this->sendMessage($player, TextFormat::AQUA . "Наш сайт: " . TextFormat::YELLOW . ServerConstants::SERVER_WEBSITE);

        if ($isNew) {
            $this->sendMessage($player, TextFormat::AQUA . "Судя по всему, вы здесь " . TextFormat::YELLOW . "новый");
        }

        $this->mainPlugin->getServer()->broadcastMessage(TextFormat::DARK_RED . $userName . TextFormat::GRAY . " зашел на сервер");

        return yield from GeneratorUtil::empty();
    }

    private function sendMessage(Player $player, string $message)
    {
        // « » ‹ ›
        $player->sendMessage(TextFormat::GOLD . TextFormat::BOLD . " Пользователи » " . TextFormat::RESET . $message);
    }
}