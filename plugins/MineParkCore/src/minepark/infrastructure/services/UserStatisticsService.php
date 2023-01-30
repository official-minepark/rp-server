<?php

namespace minepark\infrastructure\services;

use minepark\common\client\ClientResponse;
use minepark\domain\models\UserStatistic;
use minepark\domain\responses\CalculateUserLevelCommandResponse;
use minepark\infrastructure\dataservices\UserStatisticsDataService;
use minepark\infrastructure\events\UserInitializeEvent;
use minepark\infrastructure\models\UserStatesMapModel;
use minepark\infrastructure\stores\UserStatesMapStore;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class UserStatisticsService extends BaseService
{
    public function __construct(
        private UserStatisticsDataService $userStatisticsDataService
    )
    {}

    public function initializeUser(Player $user, UserStatesMapModel $statesMap): \Generator
    {
        /**
         * @var ClientResponse<UserStatistic> $response
         */
        $response = yield from $this->userStatisticsDataService->getByUserId($statesMap->profile->id);

        if (!$response->isSuccess()) {
            $response = yield from $this->userStatisticsDataService->create($statesMap->profile->id);
        }

        $statesMap->statistic = $response->getBody();

        /**
         * @var ClientResponse<CalculateUserLevelCommandResponse> $response
         */
        $response = yield from $this->userStatisticsDataService->calculateLevel($statesMap->statistic->experience);

        $levelInfo = $response->getBody();

        yield from $this->userStatisticsDataService->updateJoinedDate($statesMap->profile->id);
        $this->sendMessage($user, TextFormat::AQUA . "Вы находитесь на " . TextFormat::YELLOW . $levelInfo->level . TextFormat::AQUA . " уровне");
        $this->sendMessage($user, TextFormat::AQUA . "Для перехода на следующий уровень Вы набрали " . TextFormat::YELLOW . $levelInfo->experience . TextFormat::AQUA . " из " . TextFormat::YELLOW . $levelInfo->maximalExperience . TextFormat::AQUA . " опыта");
    }

    public function onUserQuit(Player $user, UserStatesMapModel $statesMap): \Generator
    {
        yield from $this->userStatisticsDataService->updateLeftDate($statesMap->profile->id);
    }

    public function calculateLevel(int $experience): \Generator
    {
        return yield from $this->userStatisticsDataService->calculateLevel($experience);
    }

    private function sendMessage(Player $player, string $message): void
    {
        $player->sendMessage(TextFormat::GREEN . TextFormat::BOLD . " Статистика пользователей » " . TextFormat::RESET . $message);
    }
}