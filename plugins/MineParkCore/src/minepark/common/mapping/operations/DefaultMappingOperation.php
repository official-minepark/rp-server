<?php

namespace minepark\common\mapping\operations;

use minepark\common\mapping\MappingOptions;
use minepark\common\mapping\operations\interfaces\IMappingOperation;
use minepark\common\mapping\readers\IPropertyReader;
use minepark\common\mapping\writers\IPropertyWriter;

class DefaultMappingOperation implements IMappingOperation
{
    protected MappingOptions $options;

    protected IPropertyReader $propertyReader;

    protected IPropertyWriter $propertyWriter;

    public function setOptions(MappingOptions $options): void
    {
        $this->options = $options;
        $this->propertyReader = $options->getPropertyReader();
        $this->propertyWriter = $options->getPropertyWriter();
    }

    protected function getPropertyReader(): IPropertyReader
    {
        return $this->propertyReader;
    }

    protected function getPropertyWriter(): IPropertyWriter
    {
        return $this->propertyWriter;
    }

    public function mapProperty(string $propertyName, mixed $source, mixed $destination): void
    {
        if (!$this->canMapProperty($propertyName, $source)) {
            return;
        }

        $sourceValue = $this->getSourceValue($source, $propertyName);
        $this->setDestinationValue($destination, $propertyName, $sourceValue);
    }

    protected function canMapProperty(string $propertyName, mixed $source): bool
    {
        return $this->propertyReader->hasProperty($source, $propertyName);
    }

    protected function getSourceValue($source, string $propertyName)
    {
        return $this->getPropertyReader()->getProperty(
            $source,
            $propertyName
        );
    }

    protected function setDestinationValue(
        mixed  $destination,
        string $propertyName,
        mixed  $value
    ): void
    {
        $this->getPropertyWriter()->writeProperty(
            $destination,
            $propertyName,
            $value
        );
    }
}