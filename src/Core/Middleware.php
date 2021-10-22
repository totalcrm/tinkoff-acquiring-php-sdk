<?php

namespace TotalCRM\TinkoffAcquiring\Core;

use TotalCRM\TinkoffAcquiring\Core\Http\Request;
use TotalCRM\TinkoffAcquiring\Core\Http\Response;

interface Middleware
{
    public function onRequest(Request $req): void;

    public function onResponse(Response $resp): void;

}