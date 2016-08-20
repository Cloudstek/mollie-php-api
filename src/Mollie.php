<?php

namespace Mollie\API;

use Mollie\API\Base\RequestBase;

class Mollie
{
    /** @var string API Key */
    private $api_key;

    /** @var string API Endpoint */
    private $api_endpoint = "https://api.mollie.nl/v1";

    /** @var string Locale */
    private $api_locale;

    /** @var RequestBase Request Handler */
    public $request;

    /**
     * Mollie API constructor
     *
     * @param string|null $api_key Mollie API key
     * @param string|null $api_ep Mollie API endpoint URL
     * @param RequestBase|null $requestHandler Request handler
     */
    public function __construct($api_key = null, $api_ep = null, RequestBase $requestHandler = null)
    {
        // API Key
        if (!empty($api_key)) {
            $this->setApiKey($api_key);
        }

        // API endpoint URL
        if (!empty($api_ep)) {
            $this->setApiEndpoint($api_ep);
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
        return $this->api_key;
    }

    /**
     * Set API key
     * @param string $api_key Mollie API key
     */
    public function setApiKey($api_key)
    {
        $api_key = trim($api_key);

        if (!preg_match('/^(live|test)_\w+$/', $api_key)) {
            throw new \InvalidArgumentException("Invalid Mollie API key: {$api_key}.");
        }

        $this->api_key = $api_key;
    }

    /**
     * Get API endppoint URL
     *
     * @param string $uri Endpoint URI like /customers
     * @param string[] $params URI parameters
     * @return string Complete endpoint URL to make requests to
     */
    public function getApiEndpoint($uri = null, $params = [])
    {
        $url = $this->api_endpoint;

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
     * @param string $ep API endpoint URL (without trailing slash)
     */
    public function setApiEndpoint($ep)
    {
        if (!preg_match('/^https?\:\/\//', $ep)) {
            throw new \InvalidArgumentException("Invalid Mollie API endpoint: {$ep}. Must be a valid http(s) url starting with http:// or https://.");
        }

        $ep = trim(rtrim($ep, '/'));

        $this->api_endpoint = $ep;
    }

    /**
     * Get API locale
     * @return string
     */
    public function getLocale()
    {
        return $this->api_locale;
    }

    /**
     * Set API locale
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->api_locale = $locale;
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
