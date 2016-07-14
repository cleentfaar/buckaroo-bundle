<?php

namespace TreeHouse\BuckarooBundle\Response;

use TreeHouse\BuckarooBundle\Exception\BuckarooException;

class IdealTransactionResponse extends AbstractTransactionResponse implements SignedResponseInterface
{
    /**
     * The (ideal) URL to redirect the user to to complete the transaction.
     *
     * @var string
     */
    private $redirectUrl;

    /**
     * @inheritdoc
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        parent::assertFields($data, ['BRQ_REDIRECTURL']);

        $this->redirectUrl = $data['BRQ_REDIRECTURL'];

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }
}
