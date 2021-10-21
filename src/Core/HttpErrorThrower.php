<?php

namespace TotalCRM\TinkoffAcquiring\Core;

use TotalCRM\TinkoffAcquiring\Core\Http\Request;
use TotalCRM\TinkoffAcquiring\Core\Http\Response;

class HttpErrorThrower implements Middleware {

	public function onRequest(Request $req): void {}

	public function onResponse(Response $resp): void {
		if ($resp->code != 200)
			throw new \Exception("http: ($resp->code) $resp->body", $resp->code);
	}

}