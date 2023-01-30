<?php

namespace minepark\formsapi\elements\custom;

use minepark\formsapi\elements\BaseElement;
use minepark\formsapi\elements\ValidatableElement;
use minepark\formsapi\responses\elements\DropdownResponse;
use minepark\formsapi\responses\elements\ToggleResponse;
use minepark\formsapi\responses\interfaces\IElementResponse;

class Toggle extends BaseElement implements ValidatableElement
{
    private const ELEMENT_TYPE = "toggle";

    public function __construct(
        private string $text,
        private string $elementName,
        private bool $default = false
    )
    {
        parent::__construct($this->elementName);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getDefault(): bool
    {
        return $this->default;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }

    public function validateInput(mixed $data): bool
    {
        return is_bool($data);
    }

    public function jsonSerialize(): array
    {
        return [
            "type" => self::ELEMENT_TYPE,
            "text" => $this->getText(),
            "default" => $this->getDefault()
        ];
    }

    final public function produceResponse(mixed $data): IElementResponse
    {
        return new ToggleResponse($data);
    }
}