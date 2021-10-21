<?php

namespace TotalCRM\TinkoffAcquiring\Business;

use TotalCRM\TinkoffAcquiring\Core\Http\Response;
use TotalCRM\TinkoffAcquiring\Core\Service;
use Throwable;

class Error extends \Exception {

	public function __construct(string $message = "", int $code = 0, Throwable $previous = null){
		parent::__construct($message, $code, $previous);
	}

	public int $statusCode;
	public ?string $xRequestId = NULL;
	public ?string $id = NULL;
	public ?array $details = NULL;

}