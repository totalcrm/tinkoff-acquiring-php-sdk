<?php

namespace TotalCRM\TinkoffAcquiring\Core;

use Exception;
use RuntimeException;

class JsonParser implements Parser
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
     * @param string|array|null $raw
     * @return array|null
     * @throws Exception
     */
    public function parse($raw): ?array
    {

        $decoded = null;

        if (is_string($raw)) {
            $decoded = json_decode($raw, false, 512, JSON_THROW_ON_ERROR);
        } else {
            $decoded = $raw;
        }

        if (is_null($decoded)) {
            throw new RuntimeException('wrong json: ' . $raw);
        }

        return $decoded;
    }

}