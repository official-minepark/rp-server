<?php

namespace minepark\application\views;

use minepark\common\client\ClientResponse;
use minepark\domain\responses\CalculateUserLevelCommandResponse;
use minepark\formsapi\elements\simple\Button;
use minepark\formsapi\forms\SimpleForm;
use minepark\formsapi\responses\SimpleFormResponse;
use minepark\formsapi\utilities\HiveFormUtility;
use minepark\infrastructure\services\UserStatisticsService;
use minepark\infrastructure\stores\UserStatesMapStore;
use pocketmine\player\Player;

class UserStatisticView extends BaseView
{
    public function __construct(
        private UserStatisticsService $userStatisticsService,
        private UserStatesMapStore $userStatesMapStore
    )
    {
    }

    public function sendToPlayer(Player $player): \Generator
    {
        $userInfo = $this->userStatesMapStore->getForUser($player);

        /**
         * @var ClientResponse<CalculateUserLevelCommandResponse> $response
         */
        $response = yield from $this->userStatisticsService->calculateLevel($userInfo->statistic->experience);

        $levelInfo = $response->getBody();

        $content = "Ваш уровень - " . $levelInfo->level . " (" . $levelInfo->experience . "/" . $levelInfo->maximalExperience . ")";
        $content .= "\nКоличество наигранных минут - " . $userInfo->statistic->experience;
        $content .= "\nКоличество полученных пэйдеев - " . $userInfo->statistic->paydays;

        $form = new SimpleForm(
            title: "Статистика",
            content: $content,
            buttons: [
                new Button("Вернуться")
            ]
        );

        HiveFormUtility::customizeGrid($form);

        yield from $form->sendToPlayerAsync($player);
    }
}