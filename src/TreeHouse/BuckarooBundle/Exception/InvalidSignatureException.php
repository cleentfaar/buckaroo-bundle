<?php

declare (strict_types = 1);

namespace TreeHouse\BuckarooBundle\Exception;

class InvalidSignatureException extends BuckarooException
{
    /**
     * @param string $expectedSignature
     * @param string $actualSignature
     *
     * @return InvalidSignatureException
     */
    public static function create(string $expectedSignature, string $actualSignature) : InvalidSignatureException
    {
        return new static(sprintf(
            'Invalid signature: expected "%s", got: "%s"',
            $expectedSignature,
            $actualSignature
        ));
    }
}
