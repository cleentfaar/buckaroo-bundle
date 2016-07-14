<?php

namespace TreeHouse\BuckarooBundle\Request;

use Money\Money;
use TreeHouse\BuckarooBundle\Model\Mandate;
use TreeHouse\BuckarooBundle\Response\SimpleSepaDirectDebitTransactionResponse;

class SimpleSepaDirectDebitTransactionRequest implements RequestInterface
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
     * The IBAN for the customer bank account on which the direct debit should be performed.
     *
     * @var string
     */
    private $customerIban;

    /**
     * The name of the accountholder for the account on which the direct debit should be performed.
     *
     * @var string
     */
    private $customerAccountName;

    /**
     * The BIC code for the customer bank account on which the direct debit should be performed.
     * This is only required when the IBAN is not Dutch.
     *
     * @var string|null
     */
    private $customerBic;

    /**
     * The mandate under which the direct debit falls.
     *
     * @var Mandate
     */
    private $mandate;

    /**
     * The date on which the direct debit should collected from the consumer account.
     * The actual direct debit will be sent to the bank 5 working days earlier.
     *
     * @var \DateTime|null
     */
    private $datetimeCollect;

    /**
     * @param string         $invoiceNumber
     * @param Money          $amount
     * @param Mandate        $mandate
     * @param string         $customerIban
     * @param string         $customerAccountName
     * @param null|string    $customerBic
     * @param \DateTime|null $datetimeCollect
     */
    public function __construct(
        string $invoiceNumber,
        Money $amount,
        Mandate $mandate,
        string $customerIban,
        string $customerAccountName,
        string $customerBic = null,
        \DateTime $datetimeCollect = null
    ) {
        $this->invoiceNumber = $invoiceNumber;
        $this->amount = $amount;
        $this->mandate = $mandate;
        $this->customerIban = $customerIban;
        $this->customerAccountName = $customerAccountName;
        $this->customerBic = $customerBic;
        $this->datetimeCollect = $datetimeCollect;
    }

    /**
     * @inheritdoc
     */
    public function toArray() : array
    {
        return [
            'BRQ_AMOUNT' => number_format($this->amount->getAmount() / 100, 2, '.', ''),
            'BRQ_CURRENCY' => $this->amount->getCurrency()->getCode(),
            'BRQ_INVOICENUMBER' => $this->invoiceNumber,
            'BRQ_PAYMENT_METHOD' => 'simplesepadirectdebit',
            'BRQ_COLLECTDATE' => $this->datetimeCollect ? $this->datetimeCollect->format('Y-m-d H:i:s') : '',
            'BRQ_CUSTOMERACCOUNTNAME' => $this->customerAccountName,
            'BRQ_SERVICE_SIMPLESEPADIRECTDEBIT_CUSTOMERBIC' => (string) $this->customerBic,
            'BRQ_SERVICE_SIMPLESEPADIRECTDEBIT_CUSTOMERIBAN' => $this->customerIban,
            'BRQ_SERVICE_SIMPLESEPADIRECTDEBIT_MANDATEDATE' => $this->mandate->getDate()->format('Y-m-d'),
            'BRQ_SERVICE_SIMPLESEPADIRECTDEBIT_MANDATEREFERENCE' => $this->mandate->getReference(),
            'BRQ_SERVICE_SIMPLESEPADIRECTDEBIT_ACTION' => 'Pay',
            'BRQ_STARTRECURRENT' => true,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getResponseClass() : string
    {
        return SimpleSepaDirectDebitTransactionResponse::class;
    }
}
