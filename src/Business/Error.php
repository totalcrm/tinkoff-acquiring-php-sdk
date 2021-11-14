<?php

namespace TotalCRM\TinkoffAcquiring\Business;

use TotalCRM\TinkoffAcquiring\Core\Http\Response;
use TotalCRM\TinkoffAcquiring\Core\Service;
use Exception;
use Throwable;

class Error extends Exception
{

    public int $statusCode;
    public ?string $xRequestId = null;
    public ?string $id = null;
    public ?array $details = null;

    /**
     * Error constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}