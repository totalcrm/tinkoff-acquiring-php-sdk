<?php

namespace TotalCRM\TinkoffAcquiring\Id;

use TotalCRM\TinkoffAcquiring\Core\Http\Client;
use TotalCRM\TinkoffAcquiring\Core\Http\Curl;
use TotalCRM\TinkoffAcquiring\Core\Http\Header;
use TotalCRM\TinkoffAcquiring\Core\Http\Request;
use TotalCRM\TinkoffAcquiring\Core\Http\Response;
use TotalCRM\TinkoffAcquiring\Core\Http\Ssl;
use TotalCRM\TinkoffAcquiring\Core\Middleware;

class Session implements Middleware {

	private const HEADER_NAME_AUTH = 'Authorization';
	private const AUTH_TYPE = 'Bearer';

	private AuthToken $authToken;
	private ?Ssl $ssl;
	private Client $http;

	public function __construct(AuthToken $authToken, ?Ssl $ssl = NULL, ?Client $http = NULL) {
		$this->authToken = $authToken;
		$this->ssl = $ssl;
		$this->http = $http ?? new Curl();
	}

	public function __destruct() {
		unset($this->authToken);
		unset($this->ssl);
		unset($this->http);
	}

	public function onRequest(Request $req): void {
		if ($this->authToken->isExpired()) {
			if (!$this->authToken->refreshToken)
				$this->authToken = (new AuthTokenService())->refresh($this->authToken->refreshToken);
			else
				throw new \Exception("access token is expired");
		}

		if (!$this->authToken->accessToken)
			throw new \Exception("access token must be set");

		$req->headers->set(new Header(self::HEADER_NAME_AUTH, self::AUTH_TYPE . ' ' . $this->authToken->accessToken));
		$req->ssl = $this->ssl;
	}

	public function onResponse(Response $resp): void {}

}