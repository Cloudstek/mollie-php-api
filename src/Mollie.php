<?php

namespace Mollie\API;

require_once __DIR__ . '/../vendor/composer/autoload.php';

class Mollie {

	/**
	 * API key
	 * @var string
	 */
	private $api_key;

	/**
	 * API endpoint
	 * @var string
	 */
	private $api_endpoint = "https://api.mollie.nl/v1";

	/**
	 * Request handler
	 * @var RequestInterface
	 */
	private $request;

	/**
	 * Locales
	 * @var array
	 */
	public static $locales = ['de', 'en', 'es', 'fr', 'be', 'be-fr', 'nl'];

	/**
	 * @var Resource\PaymentResource
	 */
	public $payment;

	/**
	 * @var Resource\RefundResource
	 */
	public $refund;

	/**
	 * Mollie API constructor
	 * @param string|null $api_key Mollie API key
	 * @param string|null $api_ep Mollie API endpoint URL
	 * @param RequestInterface|null
	 */
	public function __construct($api_key = null, $api_ep = null, RequestInterface $requestHandler = null) {

		// API Key
		if(!empty($api_key)) {
			$this->setApiKey($api_key);
		}

		// API endpoint URL
		if(!empty($api_ep)) {
			$this->setApiEndpoint($api_ep);
		}

		// Request handler
		$this->request = isset($requestHandler) ? $requestHandler : new Request($this);

		// Resources
		$this->payment = new Resource\PaymentResource($this);
		$this->refund = new Resource\RefundResource($this);
	}

	/**
	 * Get API key
	 * @return string
	 */
	public function getApiKey() {
		return $this->api_key;
	}

	/**
	 * Set API key
	 * @param string $api_key Mollie API key
	 */
	public function setApiKey($api_key) {
		$api_key = trim($api_key);

		if(!preg_match('/^(live|test)_\w+$/', $api_key)) {
			throw new Exception("Invalid Mollie API key: {$api_key}.");
		}

		$this->api_key = $api_key;
	}

	/**
	 * Get API endppoint URL
	 * @return string
	 */
	public function getApiEndpoint($uri = null) {
		$url = $this->api_endpoint;

		if(!empty($uri)) {
			$url .= "/" . trim(ltrim($uri, '/'));
		}

		return $url;
	}

	/**
	 * Set API endpoint URL
	 * @param string $ep API endpoint URL (without trailing slash)
	 */
	public function setApiEndpoint($ep) {
		$ep = trim(rtrim($ep, '/'));

		$this->api_endpoint = $ep;
	}

	/**
	 * Set request handler
	 * @param RequestInterface $handler
	 */
	public function setRequestHandler(RequestInterface $handler) {
		$this->request = $handler;
	}
}
