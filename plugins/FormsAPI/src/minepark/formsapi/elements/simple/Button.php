<?php

namespace minepark\formsapi\elements\simple;

use minepark\formsapi\elements\BaseElement;

class Button extends BaseElement
{
    private bool $ignored = false;

    public function __construct(
        private string $text,
        private ?ButtonImage $image = null,
        private ?string $elementName = null
    )
    {
        parent::__construct($this->elementName);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getImage(): ?ButtonImage
    {
        return $this->image;
    }

    public function isIgnored(): bool
    {
        return $this->ignored;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function setImage(?ButtonImage $image): void
    {
        $this->image = $image;
    }

    public function setIgnored(bool $ignored): void
    {
        $this->ignored = $ignored;
    }

    public function jsonSerialize(): array
    {
        $data = [
            "text" => $this->text
        ];

        if (isset($this->image)) {
            $data["image"] = $this->image->jsonSerialize();
        }

        return $data;
    }
}