<?php

namespace minepark\formsapi\forms;

use Generator;
use minepark\formsapi\constants\FormType;
use minepark\formsapi\elements\simple\Button;
use minepark\formsapi\responses\ModalFormResponse;
use minepark\formsapi\responses\SimpleFormResponse;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

/**
 * @extends BaseForm<SimpleFormResponse>
 */
class SimpleForm extends BaseForm
{
    private mixed $resolve = null;

    /**
     * @param string $title Заголовок формы
     * @param string $content Текст формы
     * @param Button[] $buttons Кнопки, все объекты должны быть Button
     */
    public function __construct(
        private string $title,
        private string $content,
        private array $buttons
    )
    {
        parent::__construct($this->title);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return Button[]
     */
    public function getButtons(): array
    {
        return $this->buttons;
    }

    public function addButton(Button $button)
    {
        $this->buttons[] = $button;
    }

    protected function getFormType(): string
    {
        return FormType::SIMPLE;
    }

    protected function serializeFormData(): array
    {
        $buttons = [];

        foreach ($this->getButtons() as $button) {
            $buttons[] = $button->jsonSerialize();
        }

        return [
            "content" => $this->content,
            "buttons" => $buttons
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if (is_null($data)) {
            $this->submitResponse(new SimpleFormResponse(null));
            return;
        }

        if (!is_int($data)) {
            throw new FormValidationException("Expected integer response, got $data");
        }

        if (!isset($this->buttons[$data])) {
            throw new FormValidationException("Got $data, which is invalid button number");
        }

        $button = $this->buttons[$data];

        if ($button->isIgnored()) {
            $this->submitResponse(new SimpleFormResponse(null));
            return;
        }

        $this->submitResponse(new SimpleFormResponse($button));
    }
}