<?php

namespace minepark\common\client\tasks;

use minepark\common\client\Client;
use minepark\common\client\ClientRequest;
use minepark\common\client\constants\HttpRequestMethod;
use pocketmine\scheduler\AsyncTask;

class AsyncHttpRequestTask extends AsyncTask
{
    private string $url;
    private array $headers;
    private string $data;
    private string $httpMethod;

    public function __construct(Client $client, ClientRequest $request, mixed $resolve)
    {
        $this->url = $client->generateUrl() . $request->getEndPoint();
        $this->headers = $request->getRawHeaders();
        $this->data = json_encode($request->getBody());
        $this->httpMethod = $request->getMethod();

        $this->storeLocal("resolve", $resolve);
    }

    public function onRun(): void
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        curl_setopt($ch, CURLOPT_POSTFIELDS, (string)$this->data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, (array)$this->headers);

        if ($this->httpMethod === HttpRequestMethod::POST) {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else if ($this->httpMethod === HttpRequestMethod::GET) {
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
        } else if ($this->httpMethod === HttpRequestMethod::PUT) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        } else if ($this->httpMethod === HttpRequestMethod::DELETE) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        curl_exec($ch);

        $resultArray = [
            "code" => curl_getinfo($ch, CURLINFO_RESPONSE_CODE),
            "result" => curl_multi_getcontent($ch)
        ];

        $this->setResult($resultArray);

        curl_close($ch);
    }

    public function onCompletion(): void
    {
        $resolve = $this->fetchLocal("resolve");
        $result = $this->getResult();

        ($resolve)($result["code"], $result["result"]);
    }
}