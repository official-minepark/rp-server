<?php

namespace minepark\common\di\traits;

use Generator;
use minepark\common\di\Context;
use minepark\common\di\interfaces\FromContext;
use RuntimeException;

trait SingletonTrait
{
    public static function getInstance(Context $context): Generator
    {
        $instance = self::getInstanceOrNull($context);

        if ($instance !== null) {
            return $instance;
        }


        $className = static::class;

        if (!is_subclass_of($className, FromContext::class)) {
            throw new RuntimeException(static::class . " has to be added into Context manually");
        }

        return yield from $context->loadOrStoreAsync($className, $className::instantiateFromContext($context));
    }

    public static function getInstanceOrNull(Context $context): ?static
    {
        return $context->getOrNull(static::class);
    }
}