<?php

namespace TotalCRM\TinkoffAcquiring\Business;

use TotalCRM\TinkoffAcquiring\Core\Parser;
use Exception;
use RuntimeException;

class BankStatementParser implements Parser
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
     * @return BankStatement|null
     * @throws Exception
     */
    public function parse($raw): ?BankStatement
    {
        $statement = new BankStatement();

        if (isset($raw->accountNumber)) {
            $statement->accountNumber = $raw->accountNumber;
        }
        if (isset($raw->saldoIn)) {
            $statement->saldoIn = $raw->saldoIn;
        }
        if (isset($raw->income)) {
            $statement->income = $raw->income;
        }
        if (isset($raw->outcome)) {
            $statement->outcome = $raw->outcome;
        }
        if (isset($raw->saldoOut)) {
            $statement->saldoOut = $raw->saldoOut;
        }

        if (isset($raw->operation)) {
            if (!is_array($raw->operation)) {
                throw new RuntimeException('wrong bank statement: ' . json_encode($raw, JSON_THROW_ON_ERROR));
            }

            $statement->operation = [];

            foreach ($raw->operation as $rawOperation) {
                $statement->operation[] = BankStatementOperationParser::instance()->parse($rawOperation);
            }
        }

        return $statement;
    }
}