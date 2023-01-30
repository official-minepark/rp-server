<?php

namespace minepark\application\views;

use minepark\common\client\ApplicationGenericError;
use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;
use minepark\formsapi\elements\simple\Button;
use minepark\formsapi\forms\SimpleForm;
use minepark\formsapi\utilities\HiveFormUtility;
use pocketmine\player\Player;

abstract class BaseView implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;

    protected function displaySuccess(Player $player, string $message): \Generator
    {
        $form = new SimpleForm(
            title: "Успех",
            content: $message,
            buttons: [
                new Button("Вернуться")
            ]
        );

        HiveFormUtility::customizeGrid($form);

        yield from $form->sendToPlayerAsync($player);
    }

    protected function displayError(Player $player, ApplicationGenericError $error): \Generator
    {
        $form = new SimpleForm(
            title: $error->serviceName,
            content: $error->message,
            buttons: [
                new Button("Вернуться")
            ]
        );

        HiveFormUtility::customizeBottomButtonPanel($form);

        return $form->sendToPlayerAsync($player);
    }
}