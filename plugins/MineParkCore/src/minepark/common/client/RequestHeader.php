<?php

namespace minepark\common\client;

class RequestHeader
{
    public function __construct(
        private string $name,
        private string $value
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}