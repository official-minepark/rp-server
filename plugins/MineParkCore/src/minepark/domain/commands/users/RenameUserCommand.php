<?php

namespace minepark\domain\commands\users;

class RenameUserCommand
{
    public function __construct(
        public int $id,
        public string $name
    )
    {}
}