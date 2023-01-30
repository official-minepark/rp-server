<?php

namespace minepark\formsapi\utilities;

use minepark\formsapi\elements\simple\Button;
use minepark\formsapi\forms\SimpleForm;

class HiveFormUtility
{
    private const GRID = "§m§a";

    private const LEFT_BUTTON_PANEL = "§m§b";

    private const BOTTOM_BUTTON_PANEL = "§m§c";

    private const IMAGE_GRID = "§m§d";

    private const BUTTON_IMAGE_DECORATION = "§m§a";

    private const SPECIAL_BUTTON = "§m§b";

    public static function customizeGrid(SimpleForm &$form): void
    {
        $form->setTitle(self::GRID . $form->getTitle());
    }

    public static function customizeImageGrid(SimpleForm &$form): void
    {
        $form->setTitle(self::IMAGE_GRID . $form->getTitle());
    }

    public static function customizeLeftButtonPanel(SimpleForm &$form): void
    {
        $form->setTitle(self::LEFT_BUTTON_PANEL . $form->getTitle());
    }

    public static function customizeBottomButtonPanel(
        SimpleForm &$form,
        ?Button $decorativeButton = null
    ): void
    {
        $form->setTitle(self::BOTTOM_BUTTON_PANEL . $form->getTitle());

        if (isset($decorativeButton)) {
            $decorativeButton->setIgnored(true);
            $decorativeButton->setText(self::BUTTON_IMAGE_DECORATION . $decorativeButton->getText());
            $form->addButton($decorativeButton);
        }
    }
}