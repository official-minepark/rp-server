<?php

namespace minepark\formsapi\elements;

use minepark\formsapi\responses\interfaces\IElementResponse;

abstract class BaseElement implements \JsonSerializable
{
    public function __construct(
        private ?string $elementName
    )
    {
    }

    public function getElementName(): ?string
    {
        return $this->elementName;
    }

    public function produceResponse(mixed $data): IElementResponse
    {
        throw new \RuntimeException("Response producing not implemented");
    }
}