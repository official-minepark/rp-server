<?php

namespace minepark\domain\commands\userprivileges;

class RemovePermissionUserPrivilegeCommand
{
    public function __construct(
        public string $name,
        public string $permission
    )
    {}
}