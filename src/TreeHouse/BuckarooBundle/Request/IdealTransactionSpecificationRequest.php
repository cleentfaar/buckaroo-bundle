<?php

namespace TreeHouse\BuckarooBundle\Request;

use TreeHouse\BuckarooBundle\Response\IdealTransactionSpecificationResponse;

class IdealTransactionSpecificationRequest implements OperationRequestInterface
{
    /**
     * @inheritdoc
     */
    public function toArray() : array
    {
        return [
            'BRQ_SERVICES' => 'ideal',
            'BRQ_LATESTVERSIONONLY' => true,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getResponseClass() : string
    {
        return IdealTransactionSpecificationResponse::class;
    }

    /**
     * @inheritdoc
     */
    public function getOperation() : string
    {
        return 'transactionrequestspecification';
    }
}
