<?php

namespace minepark\formsapi\responses;

use minepark\formsapi\elements\simple\Button;
use minepark\formsapi\responses\interfaces\IResponse;

class SimpleFormResponse implements IResponse
{
    private ?Button $button;

    public function __construct(?Button $button = null)
    {
        $this->button = $button;
    }

    public function isClosed(): bool
    {
        return !isset($this->button);
    }

    public function getButton(): ?Button
    {
        return $this->button;
    }
}