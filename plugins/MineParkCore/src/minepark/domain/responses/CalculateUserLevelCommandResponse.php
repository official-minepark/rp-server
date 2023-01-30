<?php

namespace minepark\domain\responses;

class CalculateUserLevelCommandResponse
{
    public int $level;

    public int $experience;

    public int $maximalExperience;
}