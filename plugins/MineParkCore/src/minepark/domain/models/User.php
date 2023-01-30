<?php

namespace minepark\domain\models;

class User
{
    public int $id;

    public string $name;

    public string $xuid;

    public string $privilege;

    public ?string $email;

    public string $createdDate;

    public string $updatedDate;
}