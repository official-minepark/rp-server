<?php

namespace minepark\formsapi\elements\custom;

use minepark\formsapi\elements\BaseElement;
use minepark\formsapi\elements\ValidatableElement;
use minepark\formsapi\responses\elements\DropdownResponse;
use minepark\formsapi\responses\elements\SliderResponse;
use minepark\formsapi\responses\interfaces\IElementResponse;

class Slider extends BaseElement implements ValidatableElement
{
    private const ELEMENT_TYPE = "slider";

    public function __construct(
        private string $text,
        private string $elementName,
        private int $minimalValue,
        private int $maximalValue,
        private ?int $step = null,
        private ?int $default = null,
    )
    {
        parent::__construct($this->elementName);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getMinimalValue(): int
    {
        return $this->minimalValue;
    }

    public function getMaximalValue(): int
    {
        return $this->maximalValue;
    }

    public function getStep(): ?int
    {
        return $this->step;
    }

    public function getDefault(): ?int
    {
        return $this->default;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function setMinimalValue(int $minimalValue): void
    {
        $this->minimalValue = $minimalValue;
    }

    public function setMaximalValue(int $maximalValue): void
    {
        $this->maximalValue = $maximalValue;
    }

    public function setStep(?int $step): void
    {
        $this->step = $step;
    }

    public function setDefault(?int $default): void
    {
        $this->default = $default;
    }

    public function validateInput(mixed $data): bool
    {
        if (!is_float($data) and !is_int($data)) {
            return false;
        }

        return $data <= $this->getMaximalValue() and $data >= $this->getMinimalValue();
    }

    public function jsonSerialize(): array
    {
        $data = [
            "type" => self::ELEMENT_TYPE,
            "text" => $this->getText(),
            "min" => $this->getMinimalValue(),
            "max" => $this->getMaximalValue()
        ];

        if ($this->getStep() !== null) {
            $data["step"] = $this->getStep();
        }

        if ($this->getDefault() !== null) {
            $data["default"] = $this->getDefault();
        }

        return $data;
    }

    final public function produceResponse(mixed $data): IElementResponse
    {
        return new SliderResponse($data);
    }
}