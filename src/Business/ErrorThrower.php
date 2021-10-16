<?php

namespace TotalCRM\TinkoffAcquiring\Business;

use TotalCRM\TinkoffAcquiring\Core\Http\Request;
use TotalCRM\TinkoffAcquiring\Core\Http\Response;
use TotalCRM\TinkoffAcquiring\Core\Middleware;

class ErrorThrower implements Middleware {

	public function onRequest(Request $req): void {}

	public function onResponse(Response $resp): void {
		if ($resp->code != 200)
			throw ErrorParser::instance()->parse($resp);
	}

}