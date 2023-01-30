<?php

namespace minepark\formsapi\responses;

use minepark\formsapi\responses\interfaces\IResponse;

class ModalFormResponse implements IResponse
{
    public function __construct(
        private bool $answer
    )
    {}

    public function getAnswer(): bool
    {
        return $this->answer;
    }
}