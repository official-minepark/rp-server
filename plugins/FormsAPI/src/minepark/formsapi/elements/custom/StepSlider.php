<?php

namespace minepark\formsapi\elements\custom;

use minepark\formsapi\elements\BaseElement;
use minepark\formsapi\elements\ValidatableElement;
use minepark\formsapi\responses\elements\DropdownResponse;
use minepark\formsapi\responses\elements\StepSliderResponse;
use minepark\formsapi\responses\interfaces\IElementResponse;
use pocketmine\form\FormValidationException;

class StepSlider extends BaseElement implements ValidatableElement
{
    private const ELEMENT_TYPE = "step_slider";

    public function __construct(
        private string $text,
        private string $elementName,
        private array $steps,
        private ?int $defaultIndex = null
    )
    {
        parent::__construct($this->elementName);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function getDefaultIndex(): ?int
    {
        return $this->defaultIndex;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function addStep(mixed $element): void
    {
        $this->steps[] = $element;
    }

    public function setDefaultIndex(?int $defaultIndex): void
    {
        $this->defaultIndex = $defaultIndex;
    }

    public function jsonSerialize()
    {
        $data = [
            "type" => self::ELEMENT_TYPE,
            "text" => $this->getText(),
            "steps" => $this->getSteps()
        ];

        if ($this->getDefaultIndex() !== null) {
            $data["default"] = $this->getDefaultIndex();
        }

        return $data;
    }

    public function validateInput(mixed $data): bool
    {
        if (!is_int($data)) {
            return false;
        }

        return isset($this->steps[$data]);
    }

    final public function produceResponse(mixed $data): IElementResponse
    {
        return new StepSliderResponse($this->getSteps(), $data);
    }
}