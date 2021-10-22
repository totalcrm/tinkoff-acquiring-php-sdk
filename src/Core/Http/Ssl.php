<?php

namespace TotalCRM\TinkoffAcquiring\Core\Http;

class Ssl
{

    public string $certPath;
    public string $keyPath;
    public ?string $keyPasswd;

    /**
     * Ssl constructor.
     * @param string $certPath
     * @param string $keyPath
     * @param string|null $keyPasswd
     */
    public function __construct(string $certPath, string $keyPath, ?string $keyPasswd = null)
    {
        $this->certPath = $certPath;
        $this->keyPath = $keyPath;
        $this->keyPasswd = $keyPasswd;
    }

}