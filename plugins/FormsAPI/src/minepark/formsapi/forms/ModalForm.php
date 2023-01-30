<?php

namespace minepark\formsapi\forms;

use minepark\formsapi\constants\FormType;
use minepark\formsapi\responses\CustomFormResponse;
use minepark\formsapi\responses\ModalFormResponse;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;

/**
 * @extends BaseForm<ModalFormResponse>
 */
class ModalForm extends BaseForm
{
    private mixed $resolve = null;

    public function __construct(
        private string $title,
        private string $content,
        private string $button1 = "",
        private string $button2 = ""
    )
    {
        parent::__construct($this->title);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getButton1(): string
    {
        return $this->button1;
    }

    public function getButton2(): string
    {
        return $this->button2;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setButton1(string $button1): void
    {
        $this->button1 = $button1;
    }

    public function setButton2(string $button2): void
    {
        $this->button2 = $button2;
    }

    protected function getFormType(): string
    {
        return FormType::MODAL;
    }

    protected function serializeFormData(): array
    {
        return [
            "content" => $this->getContent(),
            "button1" => $this->getButton1(),
            "button2" => $this->getButton2()
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if (!is_bool($data)) {
            throw new FormValidationException("Unable to validate form");
        }

        $this->submitResponse(new ModalFormResponse($data));
    }
}