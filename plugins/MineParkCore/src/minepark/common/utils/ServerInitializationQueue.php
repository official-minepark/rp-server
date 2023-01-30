<?php

namespace minepark\common\utils;

use SOFe\AwaitGenerator\Mutex;

final class ServerInitializationQueue
{
    private static Mutex $mutex;

    public static function initialize(): void
    {
        self::$mutex = new Mutex;
    }

    public static function getMutex(): Mutex
    {
        return self::$mutex;
    }
}