<?php

namespace minepark\domain\commands\users;

class ChangeUserEmailCommand
{
    public function __construct(
        public int $id,
        public string $email
    )
    {}
}