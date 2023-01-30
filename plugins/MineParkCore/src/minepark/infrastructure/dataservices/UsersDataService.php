<?php

namespace minepark\infrastructure\dataservices;

use Generator;
use minepark\common\client\ClientRequest;
use minepark\domain\commands\users\ChangeUserEmailCommand;
use minepark\domain\commands\users\CreateUserCommand;
use minepark\domain\commands\users\RenameUserCommand;
use minepark\domain\commands\users\SetUserPrivilegeCommand;
use minepark\domain\commands\users\TransferAccountUserCommand;
use minepark\domain\models\User;

class UsersDataService extends DataService
{
    public function getById(int $userId): Generator
    {
        $response = yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                "users/get/id",
                $userId
            )
        );

        $this->tryMappingResponse($response, User::class);

        return $response;
    }

    public function getByName(string $userName): Generator
    {
        $response = yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                "users/get/name",
                $userName
            )
        );

        $this->tryMappingResponse($response, User::class);

        return $response;
    }

    public function getByXuid(string $userXuid): Generator
    {
        $response = yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                "users/get/xuid",
                $userXuid
            )
        );

        $this->tryMappingResponse($response, User::class);

        return $response;
    }

    public function create(string $name, string $xuid, ?string $email): Generator
    {
        $command = new CreateUserCommand($name, $xuid, $email);

        $response = yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                "users/create",
                $command
            )
        );

        $this->tryMappingResponse($response, User::class);

        return $response;
    }

    public function renameUser(string $id, string $name): Generator
    {
        $command = new RenameUserCommand($id, $name);

        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                "users/rename",
                $command
            )
        );
    }

    public function changeEmail(string $id, string $email): Generator
    {
        $command = new ChangeUserEmailCommand($id, $email);

        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                "users/email",
                $command
            )
        );
    }

    public function setPrivilege(int $userId, string $privilege): Generator
    {
        $command = new SetUserPrivilegeCommand($userId, $privilege);

        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                "users/privilege",
                $command
            )
        );
    }

    public function transferAccount(int $fromId, int $toId): Generator
    {
        $command = new TransferAccountUserCommand($fromId, $toId);

        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                "users/transfer",
                $command
            )
        );
    }

    public function removeUser(int $userId): Generator
    {
        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                "users/remove",
                $userId
            )
        );
    }

    public function validateName(string $name): Generator
    {
        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                "users/validate/name",
                $name
            )
        );
    }

    public function validateEmail(string $email): Generator
    {
        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                "users/validate/email",
                $email
            )
        );
    }
}