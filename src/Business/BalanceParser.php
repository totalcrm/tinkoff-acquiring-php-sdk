<?php

namespace TotalCRM\TinkoffAcquiring\Business;

use TotalCRM\TinkoffAcquiring\Core\Parser;

class BalanceParser implements Parser
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
     * @return Balance|null
     */
    public function parse($raw): ?Balance
    {
        $balance = new Balance();

        if (isset($raw->otb)) {
            $balance->otb = $raw->otb;
        }
        if (isset($raw->authorized)) {
            $balance->authorized = $raw->authorized;
        }
        if (isset($raw->pendingPayments)) {
            $balance->pendingPayments = $raw->pendingPayments;
        }
        if (isset($raw->pendingRequisitions)) {
            $balance->pendingRequisitions = $raw->pendingRequisitions;
        }

        return $balance;
    }

}