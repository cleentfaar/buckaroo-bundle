<?php

namespace TreeHouse\BuckarooBundle\Request;

use Money\Money;
use TreeHouse\BuckarooBundle\Model\ReturnUrl;
use TreeHouse\BuckarooBundle\Response\IdealTransactionResponse;

class IdealTransactionRequest implements RequestInterface
{
    /**
     * A reference used to identify this transaction between different systems.
     *
     * @var string
     */
    private $invoiceNumber;

    /**
     * The debit amount for the transaction.
     *
     * @var Money
     */
    private $amount;

    /**
     * BIC code for the issuing bank of the consumer (e.g. INGBNL2A). Determines the redirect URL the user gets sent to.
     *
     * @var string
     */
    private $issuer;

    /**
     * The URL(s) to return the user to after he/she completes the transfer with iDeal.
     *
     * @var ReturnUrl
     */
    private $returnUrl;

    /**
     * Description of this transaction, this is also displayed in bank statements.
     *
     * @var string|null
     */
    private $description;

    /**
     * @param string      $invoiceNumber
     * @param Money       $amount
     * @param string      $issuer
     * @param ReturnUrl   $returnUrl
     * @param string|null $description
     */
    public function __construct(
        string $invoiceNumber,
        Money $amount,
        string $issuer,
        ReturnUrl $returnUrl,
        string $description = null
    ) {
        $this->invoiceNumber = $invoiceNumber;
        $this->amount = $amount;
        $this->issuer = $issuer;
        $this->returnUrl = $returnUrl;
        $this->description = $description;
    }

    /**
     * @inheritdoc
     */
    public function toArray() : array
    {
        return [
            'BRQ_AMOUNT' => number_format($this->amount->getAmount() / 100, 2, '.', ''),
            'BRQ_CURRENCY' => $this->amount->getCurrency()->getCode(),
            'BRQ_DESCRIPTION' => $this->description,
            'BRQ_INVOICENUMBER' => $this->invoiceNumber,
            'BRQ_PAYMENT_METHOD' => 'ideal',
            'BRQ_RETURN' => $this->returnUrl->getSuccess(),
            'BRQ_RETURNCANCEL' => $this->returnUrl->getCancel(),
            'BRQ_RETURNERROR' => $this->returnUrl->getError(),
            'BRQ_RETURNREJECT' => $this->returnUrl->getReject(),
            'BRQ_SERVICE_IDEAL_ACTION' => 'Pay',
            'BRQ_SERVICE_IDEAL_ISSUER' => $this->issuer,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getResponseClass() : string
    {
        return IdealTransactionResponse::class;
    }
}
