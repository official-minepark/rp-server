<?php

namespace minepark\formsapi\forms;

use minepark\formsapi\constants\FormType;
use minepark\formsapi\elements\BaseElement;
use minepark\formsapi\elements\simple\Button;
use minepark\formsapi\elements\ValidatableElement;
use minepark\formsapi\responses\CustomFormResponse;
use minepark\formsapi\responses\SimpleFormResponse;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

/**
 * @extends BaseForm<CustomFormResponse>
 */
class CustomForm extends BaseForm
{
    private mixed $resolve = null;

    /**
     * @param string $title
     * @param BaseElement[] $elements
     */
    public function __construct(
        private string $title,
        private array $elements
    )
    {
        parent::__construct($this->title);
    }

    /**
     * @return BaseElement[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    public function addElement(BaseElement $element)
    {
        $this->elements[] = $element;
    }

    protected function getFormType(): string
    {
        return FormType::CUSTOM;
    }

    protected function serializeFormData(): array
    {
        return [
            "content" => array_map(fn($element) => $element->jsonSerialize(), $this->elements)
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if ($data === null) {
            $this->submitResponse(new CustomFormResponse(null));
            return;
        }

        if (!is_array($data)) {
            throw new FormValidationException("Expected array, got " . gettype($data));
        }

        if (count($data) !== count($this->getElements())) {
            throw new FormValidationException("Data's count is not same as elements' count");
        }

        $responses = [];

        for ($index = 0; $index < count($this->getElements()); $index++) {
            $currentElement = $this->getElements()[$index];

            if ($currentElement instanceof ValidatableElement) {
                if (!$currentElement->validateInput($data[$index])) {
                    throw new FormValidationException("Data validation error");
                }
            }

            if ($currentElement->getElementName() !== null) {
                $responses[$currentElement->getElementName()] = $currentElement->produceResponse($data[$index]);
            }
        }

        $this->submitResponse(new CustomFormResponse($responses));
    }
}