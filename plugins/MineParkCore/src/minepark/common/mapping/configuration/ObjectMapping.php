<?php

namespace minepark\common\mapping\configuration;

use minepark\common\mapping\operations\interfaces\IMappingOperation;

class ObjectMapping
{
    public function __construct(
        private string $source,
        private string $destination,
        private array  $operations
    )
    {
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * @return array
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    public function forProperty(string $propertyName, IMappingOperation $mappingOperation): self
    {
        $this->operations[$propertyName] = $mappingOperation;
        return $this;
    }
}