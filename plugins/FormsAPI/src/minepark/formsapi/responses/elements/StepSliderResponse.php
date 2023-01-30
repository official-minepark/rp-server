<?php

namespace minepark\formsapi\responses\elements;

use minepark\formsapi\responses\interfaces\IElementResponse;

class StepSliderResponse implements IElementResponse
{
    public function __construct(
        private array $steps,
        private int $selectedIndex
    )
    {}

    public function getSelectedIndex(): int
    {
        return $this->selectedIndex;
    }

    public function getSelectedValue(): string
    {
        return $this->steps[$this->getSelectedIndex()];
    }
}