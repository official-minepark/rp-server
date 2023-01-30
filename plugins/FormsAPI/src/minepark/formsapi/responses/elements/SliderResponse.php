<?php

namespace minepark\formsapi\responses\elements;

use minepark\formsapi\responses\interfaces\IElementResponse;

class SliderResponse implements IElementResponse
{
    public function __construct(
        private int $value
    )
    {}

    public function getValue(): int
    {
        return $this->value;
    }
}