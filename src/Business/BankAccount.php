<?php

namespace TotalCRM\TinkoffAcquiring\Business;

class BankAccount
{

    public string $accountNumber;
    public string $name;
    public string $currency;
    public string $bankBik;
    public string $accountType;
    public Balance $balance;
    public ?TransitAccount $transitAccount = null;

    public function __destruct()
    {
        unset($this->balance, $this->transitAccount);
    }

}