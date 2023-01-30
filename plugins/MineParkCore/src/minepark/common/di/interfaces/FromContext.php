<?php

namespace minepark\common\di\interfaces;

use Generator;
use minepark\common\di\Context;

interface FromContext
{
    public static function instantiateFromContext(Context $context): Generator;
}