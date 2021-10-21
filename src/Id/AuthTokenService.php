<?php

namespace TotalCRM\TinkoffAcquiring\Id;

use TotalCRM\TinkoffAcquiring\Core\Http\Client;
use TotalCRM\TinkoffAcquiring\Core\Http\Request;
use TotalCRM\TinkoffAcquiring\Core\HttpErrorThrower;
use TotalCRM\TinkoffAcquiring\Core\JsonParser;
use TotalCRM\TinkoffAcquiring\Core\ParsersChain;
use TotalCRM\TinkoffAcquiring\Core\Service;

class AuthTokenService {

	private const URL = Service::ENDPOINT_ID . '/auth/token';

	private const REFRESH_URL = self::URL;
	private const REFRESH_METHOD = 'POST';
	private const REFRESH_HEADERS = ['Content-Type' => ['application/x-www-form-urlencoded']];

	private Service $service;

	public function __construct() {
		$this->service = new Service();
		$this->service->addMiddleware(new HttpErrorThrower());
	}

	public function __destruct() {
		unset($this->service);
	}

	public function refresh(string $refreshToken, ?Client $http = NULL): AuthToken {
		$req = new Request(self::REFRESH_URL);
		$req->headers->valuesByName = self::REFRESH_HEADERS;
		$req->body = http_build_query(['grant_type' => 'refresh_token', 'refresh_token' => $refreshToken]);
		$req->method = self::REFRESH_METHOD;

		$resp = $this->service->send($req, $http);
		$parser = new ParsersChain(JsonParser::instance(), AuthTokenParser::instance());

		return $parser->parse($resp->body);
	}

}