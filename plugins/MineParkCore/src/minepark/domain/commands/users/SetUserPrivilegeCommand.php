<?php

namespace minepark\domain\commands\users;

class SetUserPrivilegeCommand
{
    public function __construct(
        public int $userId,
        public string $privilege
    )
    {}
}