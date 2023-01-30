<?php

namespace minepark\common\mapping\operations;

use Closure;

class Operation
{
    public static function mapTo(string $destinationClass): MapToOperation
    {
        return new MapToOperation($destinationClass);
    }

    public static function ignore(): Ignore
    {
        return new Ignore();
    }

    public static function setTo(mixed $value): SetToOperation
    {
        return new SetToOperation($value);
    }

    public static function fromClosure(Closure $valueClosure): FromClosureOperation
    {
        return new FromClosureOperation($valueClosure);
    }

    public static function default(): DefaultMappingOperation
    {
        return new DefaultMappingOperation();
    }
}