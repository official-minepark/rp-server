<?php

namespace minepark\common\utils;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

final class DataStructureValidation
{
    public const TYPE_STRING = "string";

    public const TYPE_INT = "int";

    public const TYPE_FLOAT = "float";

    public const TYPE_BOOL = "bool";

    public const TYPE_ARRAY = "array";

    public const TYPE_MIXED = "mixed";

    public static function validateData(array $dataStructure, array $data): array
    {
        foreach ($data as $entry => $value) {
            if (!isset($dataStructure[$entry])) {
                unset($data[$entry]);
            }

            $type = $dataStructure[$entry];

            if (str_starts_with($type, "?")) {
                $type = substr($type, 1);

                if (is_null($value)) {
                    continue;
                }
            }

            $validationExpression = self::getTypesValidationExpressions()[$type] ?? null;

            if ($validationExpression === null) {
                throw new InvalidArgumentException("Type of structure typed in '$type', which doesn't exist");
            }

            if (!($validationExpression)($value)) {
                throw new RuntimeException("Entry $entry expected as data type $type, got " . gettype($value));
            }
        }

        return $data;
    }

    /**
     * @return array<string, Closure<bool>>
     */
    private static function getTypesValidationExpressions(): array
    {
        return [
            self::TYPE_STRING => fn($input) => is_string($input),
            self::TYPE_INT => fn($input) => is_int($input),
            self::TYPE_BOOL => fn($input) => is_bool($input),
            self::TYPE_FLOAT => fn($input) => is_float($input),
            self::TYPE_ARRAY => fn($input) => is_array($input),
            self::TYPE_MIXED => fn($input) => true
        ];
    }

    public static function generateStructureForArray(array $data): array
    {
        $structure = [];

        foreach ($data as $key => $value) {
            $type = "mixed";

            if (is_string($value)) {
                $type = "string";
            } else if (is_int($value)) {
                $type = "int";
            } else if (is_bool($value)) {
                $type = "bool";
            } else if (is_float($value)) {
                $type = "float";
            } else if (is_array($value)) {
                $type = "array";
            }

            $structure[$key] = $type;
        }

        return $structure;
    }

    public static function generateStructureForObject(string $className): array
    {
        $reflectionClass = new ReflectionClass($className);

        $structure = [];

        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $type = "mixed";

            $type = $property->getType();

            if (isset(self::getTypesValidationExpressions()[$type->getName()])) {
                $structure[$property->getName()] = $type;
            } else {
                $structure[$property->getName()] = self::TYPE_MIXED;
            }
        }

        return $structure;
    }
}