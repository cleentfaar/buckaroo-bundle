<?php

namespace TreeHouse\BuckarooBundle\Request;

interface OperationRequestInterface extends RequestInterface
{
    /**
     * @return string
     */
    public function getOperation() : string;
}
