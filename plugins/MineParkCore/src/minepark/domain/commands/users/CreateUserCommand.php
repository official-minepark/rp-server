<?php

namespace minepark\domain\commands\users;

class CreateUserCommand
{
    public function __construct(
        public string  $name,
        public string  $xuid,
        public ?string $email
    )
    {
    }
}