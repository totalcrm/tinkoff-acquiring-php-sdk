<?php

namespace TotalCRM\TinkoffAcquiring\Core\Http;

class Request
{

    public string $url;
    public Headers $headers;
    public ?string $body;
    public string $method;
    public ?Ssl $ssl;

    /**
     * Request constructor.
     * @param string $url
     * @param Headers|null $headers
     * @param string|null $body
     * @param string $method
     * @param Ssl|null $ssl
     */
    public function __construct(string $url, ?Headers $headers = null, ?string $body = null, string $method = "", ?Ssl $ssl = null)
    {
        $this->url = $url;
        $this->headers = $headers ?? new Headers();
        $this->body = $body;
        $this->method = $method;
        $this->ssl = $ssl;

        if (!$this->method) {
            if ($body) {
                $this->method = "POST";
            } else {
                $this->method = "GET";
            }
        }
    }

    public function __destruct()
    {
        unset($this->headers, $this->ssl);
    }

}