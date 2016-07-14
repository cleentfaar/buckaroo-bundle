<?php

declare (strict_types = 1);

namespace TreeHouse\BuckarooBundle\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\TransferException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use TreeHouse\BuckarooBundle\Exception\BuckarooException;
use TreeHouse\BuckarooBundle\Exception\InvalidSignatureException;
use TreeHouse\BuckarooBundle\Normalization\ResponseNormalizer;
use TreeHouse\BuckarooBundle\Request\OperationRequestInterface;
use TreeHouse\BuckarooBundle\Request\RequestInterface;
use TreeHouse\BuckarooBundle\Response\ResponseInterface;
use TreeHouse\BuckarooBundle\Response\SignedResponseInterface;
use TreeHouse\BuckarooBundle\SignatureGenerator;
use TreeHouse\BuckarooBundle\Validation\SignatureValidator;

class NvpClient
{
    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * @var SignatureGenerator
     */
    private $signatureGenerator;

    /**
     * @var SignatureValidator
     */
    private $signatureValidator;

    /**
     * @var ResponseNormalizer
     */
    private $responseNormalizer;

    /**
     * @var string
     */
    private $websiteKey;

    /**
     * @var bool
     */
    private $test;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param GuzzleClient          $client
     * @param SignatureGenerator    $signatureGenerator
     * @param SignatureValidator    $signatureValidator
     * @param ResponseNormalizer $responseNormalizer
     * @param string                $websiteKey
     * @param bool                  $test
     * @param LoggerInterface       $logger
     */
    public function __construct(
        GuzzleClient $client,
        SignatureGenerator $signatureGenerator,
        SignatureValidator $signatureValidator,
        ResponseNormalizer $responseNormalizer,
        string $websiteKey,
        bool $test = false,
        LoggerInterface $logger = null
    ) {
        $this->client = $client;
        $this->signatureGenerator = $signatureGenerator;
        $this->signatureValidator = $signatureValidator;
        $this->responseNormalizer = $responseNormalizer;
        $this->websiteKey = $websiteKey;
        $this->test = $test;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @inheritdoc
     *
     * Base method for sending requests to the Buckaroo NVP gateway.
     */
    public function send(RequestInterface $request) : ResponseInterface
    {
        $data = $request->toArray();

        $data['BRQ_WEBSITEKEY'] = $this->websiteKey;
        $data['BRQ_SIGNATURE'] = $this->signatureGenerator->generate($data);

        $responseData = $this->sendData($data, $this->getGatewayUrl($request));

        return $this->createResponse($request, $responseData);
    }

    /**
     * @param array  $data
     * @param string $url
     *
     * @return array
     *
     * @throws BuckarooException
     */
    private function sendData(array $data, string $url) : array
    {
        $requestOptions = [
            'form_params' => $data,
            'connect_timeout' => 5,
        ];

        try {
            // Log the POST request to the Buckaroo log
            $this->logger->debug($url, $requestOptions);
            $response = $this->client->post($url, $requestOptions);
        } catch (TransferException $e) {
            throw new BuckarooException(sprintf('Failed to send request to Buckaroo: %s', $e->getMessage()), null, $e);
        }

        if ($response->getStatusCode() !== 200) {
            throw new BuckarooException(sprintf(
                'The response status code is not 200 (got %s)',
                $response->getStatusCode()
            ));
        }

        $content = $response->getBody()->getContents();
        if (false === strpos($content, '=')) {
            throw new BuckarooException(sprintf(
                'No or malformed response received from the Buckaroo NVP gateway: %s',
                $content
            ));
        }

        parse_str($content, $responseData);

        return $responseData;
    }

    /**
     * @param RequestInterface $request
     * @param array            $data
     *
     * @return ResponseInterface
     */
    private function createResponse(RequestInterface $request, array $data) : ResponseInterface
    {
        $responseClass = $request->getResponseClass();
        $data = $this->responseNormalizer->normalize($data);

        if ($responseClass instanceof SignedResponseInterface) {
            // we have to remove the signature from the actual data before comparing it
            $signature = $data['BRQ_SIGNATURE'];
            unset($data['BRQ_SIGNATURE']);

            try {
                $this->signatureValidator->validate($data, $signature);
            } catch (InvalidSignatureException $e) {
                $this->logger->debug($e->getMessage(), $data);
            }
        }

        return new $responseClass($data);
    }

    /**
     * @param RequestInterface $request
     *
     * @return string
     */
    private function getGatewayUrl(RequestInterface $request) : string
    {
        $url = sprintf('https://%s.buckaroo.nl/nvp/', $this->test ? 'testcheckout' : 'checkout');

        if ($request instanceof OperationRequestInterface) {
            $url = sprintf('%s?op=%s', $url, $request->getOperation());
        }

        return $url;
    }
}
