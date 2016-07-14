<?php

namespace TreeHouse\BuckarooBundle\Response;

use TreeHouse\BuckarooBundle\Exception\BuckarooException;

abstract class AbstractTransactionResponse implements ResponseInterface
{
    /**
     * The result of the call to the gateway (see ResponseInterface).
     *
     * @var string
     */
    protected $apiResult;

    /**
     * The statuscode of the transaction (see ResponseInterface).
     *
     * @var int|null
     */
    protected $statusCode;

    /**
     * A detail status code which provides an extra explanation for the current status of the transaction.
     *
     * @var string
     */
    protected $statusCodeDetail;

    /**
     * A message explaining the current (detail)status.
     *
     * @var string|null
     */
    protected $statusMessage;

    /**
     * The time at which the payment received it current status.
     *
     * @var \DateTime
     */
    protected $timestamp;

    /**
     * The invoicenumber as provided in the request.
     *
     * @var string
     */
    protected $invoiceNumber;

    /**
     * @inheritdoc
     */
    public function __construct(array $data)
    {
        self::assertFields($data, [
            'BRQ_APIRESULT',
            'BRQ_STATUSCODE',
            'BRQ_STATUSMESSAGE',
            'BRQ_TIMESTAMP',
        ]);

        $this->apiResult = $data['BRQ_APIRESULT'];
        $this->statusCode = $data['BRQ_STATUSCODE'];
        $this->statusCodeDetail = $data['BRQ_STATUSCODE_DETAIL'] ?? null;
        $this->statusMessage = $data['BRQ_STATUSMESSAGE'];
        $this->timestamp = new \DateTime($data['BRQ_TIMESTAMP']);
    }

    /**
     * @return string
     */
    public function getApiResult()
    {
        return $this->apiResult;
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getStatusCodeDetail()
    {
        return $this->statusCodeDetail;
    }

    /**
     * @return string|null
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp() : \DateTime
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber() : string
    {
        return $this->invoiceNumber;
    }

    /**
     * @return bool
     */
    public function isInvalid() : bool
    {
        return in_array($this->statusCode, [
            self::STATUS_FAILURE,
            self::STATUS_VALIDATION_FAILURE,
            self::STATUS_TECHNICAL_ERROR,
        ]);
    }

    /**
     * @return bool
     */
    public function isPending() : bool
    {
        return in_array($this->statusCode, [
            self::STATUS_PENDING_INPUT,
            self::STATUS_PENDING_PROCESSING,
            self::STATUS_AWAITING_CONSUMER,
            self::STATUS_ON_HOLD,
        ]);
    }

    /**
     * @return bool
     */
    public function isSuccess() : bool
    {
        return self::STATUS_SUCCESS === (int) $this->statusCode;
    }

    /**
     * @param array $data
     * @param array $requiredFields
     *
     * @throws BuckarooException
     */
    protected static function assertFields(array $data, array $requiredFields)
    {
        if (!empty($diff = array_diff($requiredFields, array_keys($data)))) {
            throw new BuckarooException(sprintf(
                'Missing fields for %s: %s',
                static::class,
                implode(', ', $diff)
            ));
        }
    }
}
