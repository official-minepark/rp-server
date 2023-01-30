<?php

namespace minepark\domain\commands\userprivileges;

class CreateUserPrivilegeCommand
{
    public function __construct(
        public string $name,
        public string $displayName,
        public ?string $inherits,
        public int $priority
    )
    {}
}