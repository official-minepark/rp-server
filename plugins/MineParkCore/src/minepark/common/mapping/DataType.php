<?php

namespace minepark\common\mapping;

final class DataType
{
    public const ARRAY = "array";

    public static function isSupportedDataType(string $dataType): bool
    {
        return in_array($dataType, self::getTypeList());
    }

    private static function getTypeList(): array
    {
        return [
            self::ARRAY
        ];
    }
}