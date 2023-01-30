<?php

namespace minepark\domain\models;

class UserStatistic
{
    public int $id;

    public int $userId;

    public int $paydays;

    public int $minutesPlayed;

    public int $experience;

    public string $joinedDate;

    public string $leftDate;

    public string $createdDate;

    public string $updatedDate;
}