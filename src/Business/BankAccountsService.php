<?php

namespace TotalCRM\TinkoffAcquiring\Business;

use TotalCRM\TinkoffAcquiring\Core\ArrayParser;
use TotalCRM\TinkoffAcquiring\Core\Http\Client;
use TotalCRM\TinkoffAcquiring\Core\Http\Request;
use TotalCRM\TinkoffAcquiring\Core\JsonParser;
use TotalCRM\TinkoffAcquiring\Core\ParsersChain;
use TotalCRM\TinkoffAcquiring\Core\Service;
use TotalCRM\TinkoffAcquiring\Id\Session;
use Exception;

class BankAccountsService
{

    private const URL = Service::ENDPOINT_BUSINESS . '/api/v2/bank-accounts';

    private const GET_URL = self::URL;
    private const GET_METHOD = 'GET';
    private const GET_HEADERS = ['Content-Type' => ['application/json']];

    private Service $service;

    /**
     * BankAccountsService constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->service = new Service();
        $this->service->addMiddleware($session);
        $this->service->addMiddleware(new ErrorThrower());
    }

    public function __destruct()
    {
        unset($this->service);
    }

    /**
     * @param Client|null $http
     * @return array
     * @throws Exception
     */
    public function get(?Client $http = null): array
    {
        $req = new Request(self::GET_URL);
        $req->headers->valuesByName = self::GET_HEADERS;
        $req->method = self::GET_METHOD;

        $resp = $this->service->send($req, $http);
        $parser = new ParsersChain(JsonParser::instance(), new ArrayParser(BankAccountParser::instance()));

        return $parser->parse($resp->body);
    }

}