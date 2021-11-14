<?php

namespace TotalCRM\TinkoffAcquiring\Core\Http;

use Exception;
use RuntimeException;

class HeaderParser
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
     * @param string $headerRaw
     * @return Header
     * @throws Exception
     */
    public function parse(string $headerRaw): Header
    {
        $headerChunks = explode(":", $headerRaw, 2);

        if (count($headerChunks) !== 2) {
            throw new RuntimeException('wrong $headerRaw: ' . $headerRaw);
        }

        return new Header($headerChunks[0], $headerChunks[1]);
    }

}