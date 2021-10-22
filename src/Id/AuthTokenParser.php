<?php

namespace TotalCRM\TinkoffAcquiring\Id;

use TotalCRM\TinkoffAcquiring\Core\Parser;

class AuthTokenParser implements Parser
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
     * @param $raw
     * @return AuthToken|null
     */
    public function parse($raw): ?AuthToken
    {
        return new AuthToken(
            $raw->access_token ?? null,
            ($raw->expires_in ?? null) + time(),
            $raw->refresh_token ?? null
        );
    }

}