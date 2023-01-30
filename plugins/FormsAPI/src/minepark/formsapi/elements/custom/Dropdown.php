<?php

namespace minepark\formsapi\elements\custom;

use minepark\formsapi\elements\BaseElement;
use minepark\formsapi\elements\ValidatableElement;
use minepark\formsapi\responses\elements\DropdownResponse;
use minepark\formsapi\responses\interfaces\IElementResponse;

class Dropdown extends BaseElement implements ValidatableElement
{
    private const ELEMENT_TYPE = "dropdown";

    public function __construct(
        private string $text,
        private string $elementName,
        private array $options,
        private ?int $default = null
    )
    {
        parent::__construct($this->elementName);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getDefault(): ?int
    {
        return $this->default;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function addOption(string $text): void
    {
        $this->options[] = $text;
    }

    public function setDefault(?int $default): void
    {
        $this->default = $default;
    }

    public function jsonSerialize(): array
    {
        return [
            "type" => self::ELEMENT_TYPE,
            "text" => $this->getText(),
            "options" => $this->getOptions(),
            "default" => $this->getDefault()
        ];
    }

    public function validateInput(mixed $data): bool
    {
        if (!is_int($data)) {
            return false;
        }

        return isset($this->options[$data]);
    }

    final public function produceResponse(mixed $data): IElementResponse
    {
        return new DropdownResponse($this->getOptions(), $data);
    }
}