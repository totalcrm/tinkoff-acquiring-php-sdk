<?php

namespace TotalCRM\TinkoffAcquiring\Business;

use TotalCRM\TinkoffAcquiring\Core\Parser;

class TransitAccountParser implements Parser
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
     * @return TransitAccount|null
     */
    public function parse($raw): ?TransitAccount
    {
        $transitAccount = new TransitAccount();
        if (isset($raw->accountNumber)) {
            $transitAccount->accountNumber = $raw->accountNumber;
        }
        if (isset($raw->balance)) {
            $transitAccount->balance = $raw->balance;
        }

        return $transitAccount;
    }

}