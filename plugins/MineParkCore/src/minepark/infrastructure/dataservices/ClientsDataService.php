<?php

namespace minepark\infrastructure\dataservices;

use Generator;
use minepark\common\client\ClientRequest;
use minepark\common\client\ClientResponse;
use minepark\domain\responses\GetClientInfoQueryResponse;

class ClientsDataService extends DataService
{
    /**
     * Получает информацию о текущем клиенте. Возвращает ClientResponse<GetClientInfoQueryResponse>
     * @yield-from GetClientInfoQueryResponse
     * @return Generator<mixed, mixed, mixed, ClientResponse<GetClientInfoQueryResponse>>
     */
    public function getInfo(): Generator
    {
        /**
         * @var ClientResponse $response
         */
        $response = yield from $this->client->sendRequestAsync(
            ClientRequest::get(
                "clients/info"
            )
        );

        $this->tryMappingResponse($response, GetClientInfoQueryResponse::class);

        return $response;
    }
}