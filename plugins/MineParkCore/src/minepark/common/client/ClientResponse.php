<?php

namespace minepark\common\client;

/**
 * @template T
 */
class ClientResponse
{
    public function __construct(
        private ClientRequest            $request,
        private int                      $code,
        private mixed                    $body,
        private ?ApplicationGenericError $error = null
    )
    {
    }

    public function getRequest(): ClientRequest
    {
        return $this->request;
    }

    public function isSuccess(): bool
    {
        return str_starts_with($this->getCode(), "2");
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function isTimeout(): bool
    {
        return $this->getCode() === 0;
    }

    /**
     * @return T
     */
    public function getBody(): mixed
    {
        return $this->body;
    }

    public function getError(): ?ApplicationGenericError
    {
        return $this->error;
    }

    /**
     * @param T $body
     */
    public function setBody(mixed $body): void
    {
        $this->body = $body;
    }
}