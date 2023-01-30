<?php

namespace minepark\infrastructure\dataservices;

use minepark\common\client\ClientRequest;
use minepark\domain\commands\userprivileges\AddPermissionUserPrivilegeCommand;
use minepark\domain\commands\userprivileges\CreateUserPrivilegeCommand;
use minepark\domain\commands\userprivileges\RemovePermissionUserPrivilegeCommand;
use minepark\domain\models\UserPrivilege;
use minepark\domain\models\UserStatistic;

class UserPrivilegesDataService extends DataService
{
    public function getByName(string $name): \Generator
    {
        $response = yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-privileges/get",
                body: $name
            )
        );

        $this->tryMappingResponse($response, UserPrivilege::class);

        return $response;
    }

    public function create(string $name, string $displayName, ?string $inherits, int $priority): \Generator
    {
        $command = new CreateUserPrivilegeCommand($name, $displayName, $inherits, $priority);

        $response = yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-privileges/create",
                body: $command
            )
        );

        $this->tryMappingResponse($response, UserPrivilege::class);

        return $response;
    }

    public function addPermission(string $name, string $permission): \Generator
    {
        $command = new AddPermissionUserPrivilegeCommand($name, $permission);

        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-privileges/add-permission",
                body: $command
            )
        );
    }

    public function removePermission(string $name, string $permission): \Generator
    {
        $command = new RemovePermissionUserPrivilegeCommand($name, $permission);

        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-privileges/remove-permission",
                body: $command
            )
        );
    }

    public function calculatePermissions(string $name): \Generator
    {
        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-privileges/calculate-permissions",
                body: $name
            )
        );
    }

    public function removePrivilege(string $name): \Generator
    {
        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-privileges/remove",
                body: $name
            )
        );
    }
}