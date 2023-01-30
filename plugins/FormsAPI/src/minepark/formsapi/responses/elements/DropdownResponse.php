<?php

namespace minepark\formsapi\responses\elements;

use minepark\formsapi\responses\interfaces\IElementResponse;

class DropdownResponse implements IElementResponse
{
    public function __construct(
        private array $options,
        private int $chosenOption
    )
    {}

    public function getSelectedIndex(): int
    {
        return $this->chosenOption;
    }

    public function getSelectedValue(): string
    {
        return $this->options[$this->getSelectedIndex()];
    }
}