<?php

namespace minepark\domain\responses;

class GetClientInfoQueryResponse
{
    public string $name;

    public string $version;

    public string $productName;

    public int $protocolVersion;
}