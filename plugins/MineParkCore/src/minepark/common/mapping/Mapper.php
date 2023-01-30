<?php

namespace minepark\common\mapping;

use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;
use minepark\common\mapping\configuration\ObjectMapping;
use minepark\common\mapping\operations\interfaces\IMappingOperation;
use minepark\common\mapping\operations\interfaces\MapperAwareOperation;
use minepark\common\mapping\operations\Operation;
use minepark\common\mapping\readers\ArrayPropertyReader;
use minepark\common\mapping\readers\ObjectPropertyReader;
use minepark\common\mapping\writers\ObjectPropertyWriter;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

final class Mapper implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;

    private MappingOptions $options;

    /**
     * @var ObjectMapping[]
     */
    private array $mappings = [];

    public function map(mixed $source, string $destinationClass): mixed
    {
        if (!class_exists($destinationClass)) {
            throw new RuntimeException("Class $destinationClass doesn't exist");
        }

        if (!DataType::isSupportedDataType(gettype($source))) {
            throw new RuntimeException("Data type " . gettype($source) . " is not supported");
        }

        if ($destinationClass === DataType::ARRAY) {
            return (array)$source;
        }

        if (!is_array($source)) {
            if ($this->getMapping($source::class, $destinationClass) === null) {
                $this->createMapping($source::class, $destinationClass);
            }

            $operations = $this->getMapping($source::class, $destinationClass)->getOperations();
        } else {
            $operations = $this->generateDynamicMappingOperations($source, $destinationClass);
        }

        return $this->doMapping($source, $destinationClass, $operations);
    }

    public function getMapping(string $source, string $destination): ?ObjectMapping
    {
        foreach ($this->mappings as $mapping) {
            if ($mapping->getSource() === $source and $mapping->getDestination() === $destination) {
                return $mapping;
            }
        }

        return null;
    }

    public function createMapping(string $source, string $destination): ObjectMapping
    {
        if (!class_exists($source) or !class_exists($destination)) {
            throw new RuntimeException("Mapping $source to $destination is invalid. One of them doesn't exist.");
        }

        if ($this->getMapping($source, $destination) !== null) {
            throw new RuntimeException("Mapping $source to $destination already exists.");
        }

        $mapping = new ObjectMapping($source, $destination, $this->generateStaticMappingOperations($source, $destination));

        $this->mappings[] = $mapping;

        return $mapping;
    }

    /**
     * @param string $sourceClass
     * @param string $destinationClass
     * @return IMappingOperation[]
     */
    private function generateStaticMappingOperations(string $sourceClass, string $destinationClass): array
    {
        $sourceReflection = new ReflectionClass($sourceClass);
        $destinationReflection = new ReflectionClass($destinationClass);
        $destinationProperties = $destinationReflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $operations = [];

        foreach ($destinationProperties as $destinationProperty) {
            $destinationName = $destinationProperty->getName();
            $destinationType = $destinationProperty->getType()->getName();

            if (!$sourceReflection->hasProperty($destinationName)) {
                $operations[$destinationName] = Operation::ignore();
                continue;
            }

            $sourceProperty = $sourceReflection->getProperty($destinationName);
            $sourceType = $sourceProperty->getType()->getName();

            if ($sourceType === "object" and $destinationType === DataType::ARRAY) {
                $operations[$destinationName] = Operation::fromClosure(function (mixed $sourceObject) use ($destinationName) {
                    return $sourceObject->{$destinationName};
                });
                continue;
            }

            if ($sourceType === DataType::ARRAY and $destinationType === "object") {
                $operations[$destinationName] = Operation::mapTo($destinationType);
                continue;
            }

            $operations[$destinationName] = Operation::default();
        }

        return $operations;
    }

    /**
     * @param array $source
     * @param string $destinationClass
     * @return IMappingOperation[]
     */
    private function generateDynamicMappingOperations(array $source, string $destinationClass): array
    {
        $reflection = new ReflectionClass($destinationClass);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $operations = [];

        foreach ($properties as $property) {
            $destinationName = $property->getName();
            $destinationType = $property->getType()->getName();

            if (!array_key_exists($destinationName, $source)) {
                $operations[$destinationName] = Operation::ignore();
                continue;
            }

            $sourceValue = $source[$destinationName];
            $sourceType = gettype($sourceValue);

            if ($sourceType === "object" and $destinationType === DataType::ARRAY) {
                $operations[$destinationName] = Operation::setTo((array)$sourceValue);
                continue;
            }

            if ($sourceType === DataType::ARRAY and $destinationType === "object") {
                $operations[$destinationName] = Operation::mapTo($destinationType);
                continue;
            }

            $operations[$destinationName] = Operation::default();
        }

        return $operations;
    }

    private function doMapping(mixed $source, string $destinationClass, array $operations): mixed
    {
        $destination = new $destinationClass;

        if (is_array($source)) {
            $options = new MappingOptions(new ArrayPropertyReader(), new ObjectPropertyWriter());
        } else {
            $options = new MappingOptions(new ObjectPropertyReader(), new ObjectPropertyWriter());
        }

        foreach ($operations as $property => $operation) {
            $operation->setOptions($options);

            if ($operation instanceof MapperAwareOperation) {
                $operation->setMapper($this);
            }

            $operation->mapProperty($property, $source, $destination);
        }

        return $destination;
    }
}