<?php

namespace Mollie\API;

use Mollie\API\Base\RequestBase;

class Mollie
{
    /** @var string API Key */
    private $apiKey;

    /** @var string API Endpoint */
    private $apiEndpoint = "https://api.mollie.nl/v1";

    /** @var string Locale */
    private $apiLocale;

    /** @var RequestBase Request Handler */
    public $request;

    /**
     * Mollie API constructor
     *
     * @param string|null $apiKey Mollie API key
     * @param string|null $apiEndpoint Mollie API endpoint URL
     * @param RequestBase|null $requestHandler Request handler
     */
    public function __construct($apiKey = null, $apiEndpoint = null, RequestBase $requestHandler = null)
    {
        // API Key
        if (!empty($apiKey)) {
            $this->setApiKey($apiKey);
        }

        // API endpoint URL
        if (!empty($apiEndpoint)) {
            $this->setApiEndpoint($apiEndpoint);
        }

        // Request handler
        $this->request = isset($requestHandler) ? $requestHandler : new Request($this);
    }

    /**
     * Get API key
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set API key
     * @param string $apiKey Mollie API key
     */
    public function setApiKey($apiKey)
    {
        $apiKey = trim($apiKey);

        if (!preg_match('/^(live|test)_\w+$/', $apiKey)) {
            throw new \InvalidArgumentException("Invalid Mollie API key: {$apiKey}.");
        }

        $this->apiKey = $apiKey;
    }

    /**
     * Get API endppoint URL
     *
     * @param string $uri Endpoint URI like /customers
     * @param string[] $params URI parameters
     * @return string Complete endpoint URL to make requests to
     */
    public function getApiEndpoint($uri = null, $params = array())
    {
        $url = $this->apiEndpoint;

        if (!empty($uri)) {
            $url .= "/" . trim(trim($uri), '/');
        }

        // Build uri parameters
        if (!empty($params)) {
            $url .= "/?" . http_build_query($params);
        }

        return $url;
    }

    /**
     * Set API endpoint URL
     * @param string $apiEndpoint API endpoint URL (without trailing slash)
     */
    public function setApiEndpoint($apiEndpoint)
    {
        if (!preg_match('/^https?\:\/\//', $apiEndpoint)) {
            throw new \InvalidArgumentException("Invalid Mollie API endpoint: {$apiEndpoint}. Must be a valid http(s) url starting with http:// or https://.");
        }

        $apiEndpoint = trim(rtrim($apiEndpoint, '/'));

        $this->apiEndpoint = $apiEndpoint;
    }

    /**
     * Get API locale
     * @return string
     */
    public function getLocale()
    {
        return $this->apiLocale;
    }

    /**
     * Set API locale
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->apiLocale = $locale;
    }

    /**
     * Customer Resource
     *
     * @param Model\Customer|string $customer Customer ID
     * @return Resource\CustomerResource
     */
    public function customer($customer = null)
    {
        return new Resource\CustomerResource($this, $customer);
    }

    /**
     * Issuer Resource
     *
     * @param Model\Issuer|string $issuer Issuer ID
     * @return Resource\IssuerResource
     */
    public function issuer($issuer = null)
    {
        return new Resource\IssuerResource($this, $issuer);
    }

    /**
     * Payment Method Resource
     *
     * @param Model\Method|string $method Payment Method ID
     * @return Resource\MethodResource
     */
    public function method($method = null)
    {
        return new Resource\MethodResource($this, $method);
    }

    /**
     * Payment Resource
     *
     * @param Model\Payment|string $payment Payment ID
     * @return Resource\PaymentResource
     */
    public function payment($payment = null)
    {
        return new Resource\PaymentResource($this, $payment);
    }

    /**
     * Refund Resource
     *
     * @param Model\Refund|string $refund Refund ID
     * @return Resource\RefundResource
     */
    public function refund($refund = null)
    {
        return new Resource\RefundResource($this, $refund);
    }
}
