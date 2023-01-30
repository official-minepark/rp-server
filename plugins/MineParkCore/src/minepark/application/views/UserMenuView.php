<?php

namespace minepark\application\views;

use minepark\common\client\ClientResponse;
use minepark\formsapi\elements\custom\Input;
use minepark\formsapi\elements\simple\Button;
use minepark\formsapi\forms\CustomForm;
use minepark\formsapi\forms\SimpleForm;
use minepark\formsapi\responses\CustomFormResponse;
use minepark\formsapi\responses\elements\InputResponse;
use minepark\formsapi\responses\SimpleFormResponse;
use minepark\formsapi\utilities\HiveFormUtility;
use minepark\infrastructure\models\UserStatesMapModel;
use minepark\infrastructure\services\UsersService;
use minepark\infrastructure\stores\UserStatesMapStore;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class UserMenuView extends BaseView
{
    public function __construct(
        private UsersService $usersService,
        private UserStatesMapStore $userStatesMapStore,
        private UserSettingsView $userSettingsView,
        private UserStatisticView $userStatisticView
    )
    {}

    public function sendMainMenu(Player $player): \Generator
    {
        $form = new SimpleForm(
            title: "Главное меню",
            content: "Добро пожаловать! Выберите действие",
            buttons: [
                new Button("Аккаунт", null, "account"),
                new Button("Статистика", null, "statistic")
            ]
        );

        HiveFormUtility::customizeGrid($form);

        /**
         * @var SimpleFormResponse $response
         */
        $response = yield from $form->sendToPlayerAsync($player);

        if ($response->isClosed()) {
            return;
        }

        if ($response->getButton()->getElementName() === "account") {
            yield from $this->userSettingsView->sendToPlayer($player);
        } else if ($response->getButton()->getElementName() === "statistic") {
            yield from $this->userStatisticView->sendToPlayer($player);
        }

        yield from $this->sendMainMenu($player);
    }
}