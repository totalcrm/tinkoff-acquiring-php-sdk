<?php

namespace TotalCRM\TinkoffAcquiring\Id;

use TotalCRM\TinkoffAcquiring\Core\Http\Client;
use TotalCRM\TinkoffAcquiring\Core\Http\Curl;
use TotalCRM\TinkoffAcquiring\Core\Http\Header;
use TotalCRM\TinkoffAcquiring\Core\Http\Request;
use TotalCRM\TinkoffAcquiring\Core\Http\Response;
use TotalCRM\TinkoffAcquiring\Core\Http\Ssl;
use TotalCRM\TinkoffAcquiring\Core\Middleware;
use Exception;
use RuntimeException;

class Session implements Middleware
{

    private const HEADER_NAME_AUTH = 'Authorization';
    private const AUTH_TYPE = 'Bearer';

    private AuthToken $authToken;
    private ?Ssl $ssl;
    private Client $http;

    /**
     * Session constructor.
     * @param AuthToken $authToken
     * @param Ssl|null $ssl
     * @param Client|null $http
     */
    public function __construct(AuthToken $authToken, ?Ssl $ssl = null, ?Client $http = null)
    {
        $this->authToken = $authToken;
        $this->ssl = $ssl;
        $this->http = $http ?? new Curl();
    }

    public function __destruct()
    {
        unset($this->authToken, $this->ssl, $this->http);
    }

    /**
     * @param Request $req
     * @throws Exception
     */
    public function onRequest(Request $req): void
    {
        if ($this->authToken->isExpired()) {
            if (!$this->authToken->refreshToken) {
                $this->authToken = (new AuthTokenService())->refresh($this->authToken->refreshToken);
            } else {
                throw new RuntimeException("access token is expired");
            }
        }

        if (!$this->authToken->accessToken) {
            throw new RuntimeException("access token must be set");
        }

        $req->headers->set(new Header(self::HEADER_NAME_AUTH, self::AUTH_TYPE . ' ' . $this->authToken->accessToken));
        $req->ssl = $this->ssl;
    }

    /**
     * @param Response $resp
     */
    public function onResponse(Response $resp): void
    {
    }

}