<?php

namespace TotalCRM\TinkoffAcquiring\Core\Http;

class Response
{

    public int $code;
    public Headers $headers;
    public string $body;

    public function __destruct()
    {
        unset($this->headers);
    }

}