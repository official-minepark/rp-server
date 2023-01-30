<?php

namespace minepark\common\di\interfaces;

use Generator;
use minepark\common\di\Context;

interface Singleton
{
    public static function getInstance(Context $context): Generator;

    public static function getInstanceOrNull(Context $context): ?static;
}