<?php

namespace minepark\formsapi\responses\elements;

use minepark\formsapi\responses\interfaces\IElementResponse;

class ToggleResponse implements IElementResponse
{
    public function __construct(
        private bool $status
    )
    {}

    public function getStatus(): bool
    {
        return $this->status;
    }
}