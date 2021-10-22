<?php

namespace TotalCRM\TinkoffAcquiring\Core;

use TotalCRM\TinkoffAcquiring\Core\Http\Client;
use TotalCRM\TinkoffAcquiring\Core\Http\Curl;
use TotalCRM\TinkoffAcquiring\Core\Http\Request;
use TotalCRM\TinkoffAcquiring\Core\Http\Response;
use Exception;

class Service
{

    public const ENDPOINT_ID = 'https://id.tinkoff.ru';
    public const ENDPOINT_BUSINESS = 'https://business.tinkoff.ru/openapi';
    public const ENDPOINT_SECURED_BUSINESS = 'https://secured-openapi.business.tinkoff.ru';
    public const HEADER_NAME_REQUEST_ID = 'X-Request-Id';
    private array $middlewares = [];

    public function __destruct()
    {
        foreach ($this->middlewares as $key => $value) {
            unset($this->middlewares[$key]);
        }
    }

    /**
     * @param Middleware $middleware
     */
    public function addMiddleware(Middleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @param Request $req
     * @param Client|null $http
     * @return Response
     * @throws Exception
     */
    public function send(Request $req, ?Client $http = null): Response
    {
        $http = $http ?? new Curl();

        foreach ($this->middlewares as $middleware) {
            $middleware->onRequest($req);
        }

        $resp = $http->send($req);

        foreach (array_reverse($this->middlewares) as $middleware) {
            $middleware->onResponse($resp);
        }

        return $resp;
    }

}