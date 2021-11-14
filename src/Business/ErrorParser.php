<?php

namespace TotalCRM\TinkoffAcquiring\Business;

use TotalCRM\TinkoffAcquiring\Core\Http\Response;
use TotalCRM\TinkoffAcquiring\Core\Service;

class ErrorParser
{

    private static ?self $instance = null;

    private function __construct()
    {
    }

    public static function instance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param Response $resp
     * @return Error
     */
    public function parse(Response $resp): Error
    {
        $data = json_decode($resp->body, true, 512, JSON_THROW_ON_ERROR);

        $message = $data['errorMessage'] ?? "http: ($resp->code)";

        if (isset($data['errorCode'])) {
            $message = "$data[errorCode]: $message";
        }

        $error = new Error($message, $resp->code);
        $error->xRequestId = $resp->headers->get(Service::HEADER_NAME_REQUEST_ID)[0] ?? null;
        $error->id = $data['errorId'] ?? null;
        $error->details = $data['errorDetails'] ?? null;

        return $error;
    }

}