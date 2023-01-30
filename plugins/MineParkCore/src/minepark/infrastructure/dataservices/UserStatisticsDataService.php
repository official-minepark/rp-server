<?php

namespace minepark\infrastructure\dataservices;

use Generator;
use minepark\common\client\ClientRequest;
use minepark\domain\commands\userstatistics\AddUserExperienceCommand;
use minepark\domain\models\UserStatistic;
use minepark\domain\responses\CalculateUserLevelCommandResponse;

class UserStatisticsDataService extends DataService
{
    public function getByUserId(int $userId): Generator
    {
        $response = yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-statistics/user-id",
                body: $userId
            )
        );

        $this->tryMappingResponse($response, UserStatistic::class);

        return $response;
    }

    public function create(int $userId): Generator
    {
        $response = yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-statistics/create",
                body: $userId
            )
        );

        $this->tryMappingResponse($response, UserStatistic::class);

        return $response;
    }

    public function addExperience(int $userId, int $experience): Generator
    {
        $command = new AddUserExperienceCommand($userId, $experience);

        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-statistics/add-experience",
                body: $command
            )
        );
    }

    public function calculateLevel(int $experience): Generator
    {
        $response = yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-statistics/calculate-level",
                body: $experience
            )
        );

        $this->tryMappingResponse($response, CalculateUserLevelCommandResponse::class);

        return $response;
    }

    public function accruePayday(int $userId): Generator
    {
        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-statistics/accrue-payday",
                body: $userId
            )
        );
    }

    public function updateJoinedDate(int $userId): Generator
    {
        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-statistics/joined-date",
                body: $userId
            )
        );
    }

    public function updateLeftDate(int $userId): Generator
    {
        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-statistics/left-date",
                body: $userId
            )
        );
    }

    public function remove(int $userId): Generator
    {
        return yield from $this->client->sendRequestAsync(
            ClientRequest::post(
                endPoint: "user-statistics/remove",
                body: $userId
            )
        );
    }
}