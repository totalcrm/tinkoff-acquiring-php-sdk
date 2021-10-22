<?php

namespace TotalCRM\TinkoffAcquiring\Core\Http;

use Exception;

class HeadersParser
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
     * @param string|null $headersRaw
     * @return Headers
     * @throws Exception
     */
    public function parse(?string $headersRaw = ''): Headers
    {
        $headersRaw = trim($headersRaw);
        $headersRaw = explode("\r\n", $headersRaw);

        if (strpos($headersRaw[0], "HTTP/") === 0) {
            array_shift($headersRaw);
        }

        $headers = new Headers();

        foreach ($headersRaw as $headerRaw) {
            $headers->add(HeaderParser::instance()->parse($headerRaw));
        }

        return $headers;
    }

}