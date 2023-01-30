<?php

namespace minepark\formsapi\responses;


use minepark\formsapi\responses\interfaces\IElementResponse;
use minepark\formsapi\responses\interfaces\IResponse;

class CustomFormResponse implements IResponse
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private ?array $data
    )
    {
        if (!isset($this->data)) {
            return;
        }

        foreach ($data as $elementKey => $elementResponse) {
            if (!$elementResponse instanceof IElementResponse) {
                throw new \RuntimeException("In custom form response there can't be element of type " . gettype($elementResponse));
            }
        }
    }

    /**
     * @return array<string, IElementResponse>|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    public function isClosed(): bool
    {
        return !isset($this->data);
    }

    /**
     * @template T
     * @param string $responseKey
     * @param class-string<T> $elementClass
     * @return T
     */
    public function getResponse(string $responseKey, string $elementClass): IElementResponse
    {
        $response = $this->getData()[$responseKey];

        if ($response === null) {
            throw new \RuntimeException("There's no element with name $responseKey");
        }

        if ($response::class !== $elementClass) {
            throw new \RuntimeException("Types not same. Needed " . $elementClass . ", got " . gettype($response));
        }

        return $response;
    }
}