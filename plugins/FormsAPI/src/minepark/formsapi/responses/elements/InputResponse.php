<?php

namespace minepark\formsapi\responses\elements;

use minepark\formsapi\responses\interfaces\IElementResponse;

class InputResponse implements IElementResponse
{
    public function __construct(
        private string $input
    )
    {}

    public function getInput(): string
    {
        return $this->input;
    }
}