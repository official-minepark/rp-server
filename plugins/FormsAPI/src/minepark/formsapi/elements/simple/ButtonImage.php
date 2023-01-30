<?php

namespace minepark\formsapi\elements\simple;

use minepark\formsapi\constants\ImageType;

class ButtonImage implements \JsonSerializable
{
    public function __construct(
        private string $data,
        private string $type
    )
    {
        if ($this->type !== ImageType::URL and $this->type !== ImageType::PATH) {
            throw new \RuntimeException("Unknown image type in form: " . $this->type);
        }
    }

    public function jsonSerialize(): array
    {
        return [
            "type" => $this->type,
            "data" => $this->data
        ];
    }
}