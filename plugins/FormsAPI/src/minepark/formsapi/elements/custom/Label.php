<?php

namespace minepark\formsapi\elements\custom;

use minepark\formsapi\elements\BaseElement;
use minepark\formsapi\elements\ValidatableElement;

class Label extends BaseElement implements ValidatableElement
{
    private const ELEMENT_TYPE = "label";

    public function __construct(
        private string $text
    )
    {
        parent::__construct(null);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function validateInput(mixed $data): bool
    {
        return is_null($data);
    }

    public function jsonSerialize(): array
    {
        return [
            "type" => self::ELEMENT_TYPE,
            "text" => $this->text
        ];
    }
}