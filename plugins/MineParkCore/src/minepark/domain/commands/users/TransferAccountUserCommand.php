<?php

namespace minepark\domain\commands\users;

class TransferAccountUserCommand
{
    public function __construct(
        public int $fromId,
        public int $toId
    )
    {}
}