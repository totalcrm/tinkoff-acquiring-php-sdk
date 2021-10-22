<?php

namespace TotalCRM\TinkoffAcquiring\Business;

use TotalCRM\TinkoffAcquiring\Business\BankStatement;
use TotalCRM\TinkoffAcquiring\Core\Http\Client;
use TotalCRM\TinkoffAcquiring\Core\Http\Request;
use TotalCRM\TinkoffAcquiring\Core\JsonParser;
use TotalCRM\TinkoffAcquiring\Core\ParsersChain;
use TotalCRM\TinkoffAcquiring\Core\Service;
use TotalCRM\TinkoffAcquiring\Id\Session;
use Exception;
use DateTime;

class BankStatementService
{

    private const URL = Service::ENDPOINT_BUSINESS . '/api/v1/bank-statement';

    private const GET_URL = self::URL;
    private const GET_METHOD = 'GET';
    private const GET_HEADERS = ['Content-Type' => ['application/json']];

    private Service $service;

    /**
     * BankStatementService constructor.
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
     * @param string $accountNumber
     * @param DateTime|null $from
     * @param DateTime|null $till
     * @param Client|null $http
     * @return BankStatement
     * @throws Exception
     */
    public function get(string $accountNumber, ?DateTime $from = null, ?DateTime $till = null, ?Client $http = null): BankStatement
    {
        $queryData = ['accountNumber' => $accountNumber];

        if ($from) {
            $queryData['from'] = $from->format('Y-m-d');
        }

        if ($till) {
            $queryData['till'] = $till->format('Y-m-d');
        }

        $url = self::GET_URL . '?' . http_build_query($queryData);

        $req = new Request($url);
        $req->headers->valuesByName = self::GET_HEADERS;
        $req->method = self::GET_METHOD;

        $resp = $this->service->send($req, $http);
        $parser = new ParsersChain(JsonParser::instance(), BankStatementParser::instance());

        /** @var BankStatement $result */
        $result = $parser->parse($resp->body);
        return $result;
    }


}