<?php

declare (strict_types = 1);

namespace TreeHouse\BuckarooBundle\Validation;

use TreeHouse\BuckarooBundle\Exception\InvalidSignatureException;
use TreeHouse\BuckarooBundle\SignatureGenerator;

class SignatureValidator
{
    /**
     * @var SignatureGenerator
     */
    private $signatureGenerator;

    /**
     * @param SignatureGenerator $signatureGenerator
     */
    public function __construct(SignatureGenerator $signatureGenerator)
    {
        $this->signatureGenerator = $signatureGenerator;
    }

    /**
     * @param array  $responseDataWithoutSignature
     * @param string $signature
     *
     * @throws InvalidSignatureException
     */
    public function validate(array $responseDataWithoutSignature, string $signature)
    {
        $expectedSignature = $this->signatureGenerator->generate($responseDataWithoutSignature);

        if ($signature !== $expectedSignature) {
            throw InvalidSignatureException::create($expectedSignature, $signature);
        }
    }
}
