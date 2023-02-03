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
use minepark\infrastructure\services\UsersService;
use minepark\infrastructure\stores\UserStatesMapStore;
use pocketmine\player\Player;

class UserSettingsView extends BaseView
{
    public function __construct(
        private UsersService $usersService,
        private UserStatesMapStore $userStatesMapStore
    )
    {}

    public function sendToPlayer(Player $player): \Generator
    {
        $form = new SimpleForm(
            title: "Аккаунт",
            content: "Настройки аккаунта",
            buttons: [
                new Button("Cменить почту", null, "changeEmail")
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

        if ($response->getButton()->getElementName() === "changeEmail") {
            yield from $this->sendChangeEmailForm($player);
        }

        yield from $this->sendToPlayer($player);
    }

    private function sendChangeEmailForm(Player $player): \Generator
    {
        $oldEmail = $this->userStatesMapStore->getUser($player)->profile->email;

        $form = new CustomForm(
            title: "Смена почты",
            elements: [
                new Input("Введите свою почту", "email", null, $oldEmail)
            ]
        );

        /**
         * @var CustomFormResponse $response
         */
        $response = yield from $form->sendToPlayerAsync($player);

        if ($response->isClosed()) {
            return;
        }

        $email = $response->getResponse("email", InputResponse::class)->getInput();

        /**
         * @var ClientResponse $response
         */
        $response = yield from $this->usersService->changeUserEmail($player, $email);

        if (!$response->isSuccess()) {
            yield from $this->displayError($player, $response->getError());
            yield from $this->sendChangeEmailForm($player);
        }

        yield from $this->displaySuccess($player, "Ваша почта успешно сменена на '$email'");
    }
}