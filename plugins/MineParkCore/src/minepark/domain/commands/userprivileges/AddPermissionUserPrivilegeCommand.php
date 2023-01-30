<?php

namespace minepark\domain\commands\userprivileges;

class AddPermissionUserPrivilegeCommand
{
    public function __construct(
        public string $name,
        public string $permission
    )
    {}
}