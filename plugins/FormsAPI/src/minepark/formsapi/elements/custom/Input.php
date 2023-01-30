<?php

namespace minepark\formsapi\elements\custom;

use minepark\formsapi\elements\BaseElement;
use minepark\formsapi\elements\ValidatableElement;
use minepark\formsapi\responses\elements\DropdownResponse;
use minepark\formsapi\responses\elements\InputResponse;
use minepark\formsapi\responses\interfaces\IElementResponse;

class Input extends BaseElement implements ValidatableElement
{
    private const ELEMENT_TYPE = "input";

    public function __construct(
        private string $text,
        private string $elementName,
        private ?string $placeHolder = null,
        private ?string $default = null
    )
    {
        parent::__construct($this->elementName);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getPlaceHolder(): ?string
    {
        return $this->placeHolder;
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function setPlaceHolder(?string $placeHolder): void
    {
        $this->placeHolder = $placeHolder;
    }

    public function setDefault(?string $default): void
    {
        $this->default = $default;
    }

    public function jsonSerialize(): array
    {
        return [
            "type" => self::ELEMENT_TYPE,
            "text" => $this->getText(),
            "placeholder" => $this->getPlaceHolder(),
            "default" => $this->getDefault()
        ];
    }

    public function validateInput(mixed $data): bool
    {
        return is_string($data);
    }

    final public function produceResponse(mixed $data): IElementResponse
    {
        return new InputResponse($data);
    }
}