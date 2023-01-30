<?php

namespace minepark\domain\models;

class UserPrivilege
{
    public string $name;

    public string $displayName;

    public array $permissions;

    public ?string $inherits;

    public int $priority;
}