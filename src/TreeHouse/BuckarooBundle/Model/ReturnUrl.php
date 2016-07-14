<?php

declare (strict_types = 1);

namespace TreeHouse\BuckarooBundle\Model;

class ReturnUrl
{
    /**
     * URL to return the user to when the payment was successful.
     *
     * @var string
     */
    private $success;

    /**
     * URL to return the user to when the transaction was cancelled (by the user).
     *
     * @var string|null
     */
    private $cancel;

    /**
     * URL to return the user to when an error occurred during the transaction.
     *
     * @var string|null
     */
    private $error;

    /**
     * URL to return the user to when the transaction was rejected by their bank.
     *
     * @var string|null
     */
    private $reject;

    /**
     * @param string      $success
     * @param string|null $cancel
     * @param string|null $error
     * @param string|null $reject
     */
    public function __construct(
        string $success,
        string $cancel = null,
        string $error = null,
        string $reject = null
    ) {
        $this->success = $success;
        $this->cancel = $cancel;
        $this->error = $error;
        $this->reject = $reject;
    }

    /**
     * @return string
     */
    public function getSuccess() : string
    {
        return $this->success;
    }

    /**
     * @return string|null
     */
    public function getCancel()
    {
        return $this->cancel;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getReject()
    {
        return $this->reject;
    }
}
