<?php

namespace minepark\common\di\traits;

use Generator;
use minepark\common\di\Context;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

trait SingletonArgsTrait
{
    public static function instantiateFromContext(Context $context): Generator
    {
        $reflectionClass = new ReflectionClass(static::class);

        try {
            $function = $reflectionClass->getMethod("fromSingletonArgs");

            if (!$function->isStatic()) {
                throw new RuntimeException("{$reflectionClass->getName()}::{$function->getName()}() must be static");
            }

            $constructor = fn($args) => $function->invokeArgs(null, $args);
        } catch (ReflectionException $_) {
            $function = $reflectionClass->getConstructor();

            if ($function === null) {
                return $reflectionClass->newInstance();
            }

            $constructor = fn($args) => $reflectionClass->newInstanceArgs($args);
        }

        $args = yield from $context->resolveArgs($function, static::class);

        $ret = $constructor($args);

        if ($ret instanceof Generator) {
            $ret = yield from $ret;
        }

        return $ret;
    }
}