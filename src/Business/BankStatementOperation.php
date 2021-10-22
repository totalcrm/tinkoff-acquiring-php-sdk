<?php

namespace TotalCRM\TinkoffAcquiring\Business;

class BankStatementOperation
{

    public string $id;
    public int $amount;
    public string $date;
    public string $drawDate;
    public string $chargeDate;
    public string $operationType;
    public string $paymentPurpose;
    public string $creatorStatus;

    public string $payerName;
    public ?string $payerInn = null;
    public ?string $payerAccount = null;
    public ?string $payerCorrAccount = null;
    public string $payerBic;
    public string $payerBank;
    public ?string $payerKpp = null;

    public string $recipient;
    public ?string $recipientInn = null;
    public string $recipientAccount;
    public ?string $recipientCorrAccount = null;
    public string $recipientBic;
    public string $recipientBank;
    public ?string $recipientKpp = null;

    public ?string $paymentType = null;
    public ?string $uin = null;
    public ?string $kbk = null;
    public ?string $oktmo = null;
    public ?string $taxEvidence = null;
    public ?string $taxPeriod = null;
    public ?string $taxDocNumber = null;
    public ?string $taxDocDate = null;
    public ?string $taxType = null;
    public ?string $executionOrder = null;

}