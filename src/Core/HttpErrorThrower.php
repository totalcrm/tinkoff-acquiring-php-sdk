<?php

namespace TotalCRM\TinkoffAcquiring\Core;

use TotalCRM\TinkoffAcquiring\Core\Http\Request;
use TotalCRM\TinkoffAcquiring\Core\Http\Response;
use Exception;
use RuntimeException;

class HttpErrorThrower implements Middleware
{
    public function onRequest(Request $req): void
    {
    }

    /**
     * @param Response $resp
     * @throws Exception
     */
    public function onResponse(Response $resp): void
    {
        if ($resp->code !== 200) {
            throw new RuntimeException("http: ($resp->code) $resp->body", $resp->code);
        }
    }

}