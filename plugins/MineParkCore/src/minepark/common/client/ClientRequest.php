<?php

namespace minepark\common\client;

use minepark\common\client\constants\HttpRequestMethod;

class ClientRequest
{
    /**
     * @param string $method
     * @param string $endPoint
     * @param mixed $body
     * @param RequestHeader[] $headers
     */
    private function __construct(
        private string $method,
        private string $endPoint,
        private mixed  $body,
        private array  $headers
    )
    {
    }

    public static function get(string $endPoint, array $headers = []): self
    {
        return new self(HttpRequestMethod::GET, $endPoint, null, $headers);
    }

    public static function post(string $endPoint, mixed $body, array $headers = []): self
    {
        return new self(HttpRequestMethod::POST, $endPoint, $body, $headers);
    }

    public static function put(string $endPoint, mixed $body, array $headers = []): self
    {
        return new self(HttpRequestMethod::PUT, $endPoint, $body, $headers);
    }

    public static function delete(string $endPoint, mixed $body, array $headers = []): self
    {
        return new self(HttpRequestMethod::DELETE, $endPoint, $body, $headers);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function getEndPoint(): string
    {
        return $this->endPoint;
    }

    public function getRawHeaders(): array
    {
        $rawHeaders = [];

        foreach ($this->getHeaders() as $header) {
            $rawHeaders[] = $header->getName() . ": " . $header->getValue();
        }

        return $rawHeaders;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setEndPoint(string $endPoint): void
    {
        $this->endPoint = $endPoint;
    }

    public function setBody(mixed $body): void
    {
        $this->body = $body;
    }

    /**
     * @param RequestHeader[] $headers
     * @return void
     */
    public function addHeaders(array $newHeaders): void
    {
        foreach ($newHeaders as $header) {
            $existingHeader = $this->getHeader($header->getName());

            if ($existingHeader === null) {
                $this->headers[] = $header;
                continue;
            }

            $existingHeader->setValue($existingHeader->getValue());
        }
    }

    public function getHeader(string $headerName): ?RequestHeader
    {
        foreach ($this->headers as $header) {
            if ($header->getName() === $headerName) {
                return $header;
            }
        }

        return null;
    }
}