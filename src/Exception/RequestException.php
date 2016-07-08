<?php

namespace Mollie\API\Exception;

class RequestException extends \Exception {

	/**
	 * Response
	 * @var mixed
	 */
	private $response;

	/**
	 * Request exception constructor
	 * @param string $message
	 * @param int $code
	 * @param string $url
	 * @param string $response
	 */
	public function __construct($message, $code = 0, $url = "", $response = null) {
		$this->response = $response;

		if(!empty($url)) {
			$message .= ": [{$code}][{$url}]";
		}

		if(!empty($response) && !empty($response->body->error)) {
			$message .= ": {$response->body->error->message}.";
		}

		parent::__construct($message, $code);
	}

	/**
	 * Get response
	 * @return mixed
	 */
	public function getResponse() {
		return $this->response;
	}
}
