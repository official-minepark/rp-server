<?php

namespace minepark\domain\commands\userstatistics;

class AddUserExperienceCommand
{
    public function __construct(
        public int $userId,
        public int $experience
    )
    {}
}