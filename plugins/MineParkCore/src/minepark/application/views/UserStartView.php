<?php

namespace minepark\application\views;

use minepark\application\views\responses\UserStartViewResponse;
use minepark\common\client\ApplicationGenericError;
use minepark\common\client\ClientResponse;
use minepark\formsapi\elements\custom\Input;
use minepark\formsapi\elements\custom\Label;
use minepark\formsapi\elements\simple\Button;
use minepark\formsapi\forms\CustomForm;
use minepark\formsapi\forms\SimpleForm;
use minepark\formsapi\responses\CustomFormResponse;
use minepark\formsapi\responses\elements\InputResponse;
use minepark\formsapi\responses\SimpleFormResponse;
use minepark\infrastructure\dataservices\UsersDataService;
use pocketmine\player\Player;

class UserStartView extends BaseView
{
    public function __construct(
        private UsersDataService $usersDataService
    )
    {}

    public function sendToPlayer(Player $player): \Generator
    {
        yield from $this->sendStartupForm($player);

        $name = yield from $this->sendSetNameForm($player);
        $email = yield from $this->sendSetEmailForm($player);
        yield from $this->sendFinalForm($player);

        return new UserStartViewResponse($name, $email);
    }

    private function sendStartupForm(Player $player): \Generator
    {
        $form = new SimpleForm(
            title: "Начало игры",
            content: "Добро пожаловать на сервер MinePark!",
            buttons: [
                new Button("Начать")
            ]
        );

        /**
         * @var SimpleFormResponse $response
         */
        $response = yield from $form->sendToPlayerAsync($player);

        if ($response->isClosed()) {
            return yield from $this->sendStartupForm($player);
        }
    }

    private function sendSetNameForm(Player $player): \Generator
    {
        $form = new CustomForm(
            title: "Установка имени",
            elements: [
                new Input("Для начала игры стоит ввести полное имя. Оно должно соответствовать правилам RP и содержать в себе от 3 до 25 символов. Имя пользователя должно состоять из английских букв и символа подчёркивания в формате Имя_Фамилия. В ином случае администраторы принудительно сменят его.", "name")
            ]
        );

        /**
         * @var CustomFormResponse $response
         */
        $response = yield from $form->sendToPlayerAsync($player);

        if ($response->isClosed()) {
            return yield from $this->sendSetNameForm($player);
        }

        $name = $response->getResponse("name", InputResponse::class)->getInput();

        /**
         * @var ClientResponse $response
         */
        $response = yield from $this->usersDataService->validateName($name);

        if (!$response->isSuccess()) {
            yield from $this->displayError($player, $response->getError());
            return yield from $this->sendSetNameForm($player);
        }

        return $name;
    }

    private function sendSetEmailForm(Player $player): \Generator
    {
        $form = new CustomForm(
            title: "Ввод почты",
            elements: [
                new Input("Отлично! Теперь введите свою почту. Вы можете оставить данное поле пустым.", "email")
            ]
        );

        /**
         * @var CustomFormResponse $response
         */
        $response = yield from $form->sendToPlayerAsync($player);

        if ($response->isClosed()) {
            return yield from $this->sendSetEmailForm($player);
        }

        $email = $response->getResponse("email", InputResponse::class)->getInput();

        $email = $email === "" ? null : $email;

        if ($email !== null) {
            /**
             * @var ClientResponse $response
             */
            $response = yield from $this->usersDataService->validateEmail($email);

            if (!$response->isSuccess()) {
                yield from $this->displayError($player, $response->getError());
                return yield from $this->sendSetEmailForm($player);
            }
        }

        return $email;
    }

    private function sendFinalForm(Player $player): \Generator
    {
        $form = new SimpleForm(
            title: "Все готово!",
            content: "Отлично, теперь все готово! Готовы к игре?",
            buttons: [
                new Button("Начать игру")
            ]
        );

        /**
         * @var SimpleFormResponse $response
         */
        $response = yield from $form->sendToPlayerAsync($player);

        if ($response->isClosed()) {
            return yield from $this->sendFinalForm($player);
        }
    }
}