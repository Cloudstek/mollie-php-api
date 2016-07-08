<?php

namespace Mollie\API\Resource;

use Mollie\API\Mollie;
use Mollie\API\Model\Payment;

class PaymentResource extends ResourceBase {

	/**
	 * Get payment
	 * @param string $id Payment ID
	 * @return Payment
	 */
	public function get($id) {
		$resp = $this->api->get("/payments/{$id}");

		// Return payment model
		return new Payment($resp);
	}

	/**
	 * Get all payments
	 * @return Generator|Payment[]
	 */
	public function all() {
		$items = $this->api->getAll("/payments");

		foreach($items as $item) {
			yield new Payment($item);
		}
	}

	/**
	 * Create payment
	 * @see https://www.mollie.com/nl/docs/reference/payments/create
	 * @param double $amount The amount in EURO that you want to charge
	 * @param string $description The description of the payment you're creating.
	 * @param string $redirectUrl The URL the consumer will be redirected to after the payment process.
	 * @param string $webhookUrl Use this parameter to set a webhook URL for this payment only.
	 * @param string $method Payment method to use, leave blank to use payment method selection screen
	 * @param array $methodParams Payment method specific parameters
	 * @param array $metadata Metadata for this payment
	 * @param string $locale Allow you to preset the language to be used in the payment screens shown to the consumer.
	 * @param string $recurringType
	 * @return Payment
	 */
	public function create($amount, $description, $redirectUrl, $webhookUrl = null, $method = null, array $methodParams = null, array $metadata = null, $locale = null, $recurringType = null) {

		// Check payment method
		if(!empty($method)) {
			if(!in_array($method, Payment::methods)) {
				throw new InvalidArgumentException("Invalid payment method '{$method}'. Please see https://www.mollie.com/nl/docs/reference/payments/create for available payment methods.");
			}
		}

		// Check locale
		if(!empty($locale)) {
			if(!in_array($locale, Mollie::locales)) {
				$locale = null; // Use browser language
			}
		}

		// Check recurring type
		if(!empty($recurringType)) {
			if($recurringType != "first" && $recurringType != "recurring") {
				throw new InvalidArgumentException("Invalid recurring type '{$recurringType}'. Recurring type must be 'first' or 'recurring'.");
			}
		}

		// Convert metadata to JSON
		if(!empty($metadata)) {
			$metadata = json_encode($metadata);
		}

		// Construct parameters
		$params = [
			'amount'		=> $amount,
			'description'	=> $description,
			'redirectUrl'	=> $redirectUrl,
			'webhookUrl'	=> $webhookUrl,
			'method'		=> $method,
			'metadata'		=> $metadata,
			'locale'		=> $locale,
			'recurringType'	=> $recurringType
		];

		// Append method parameters if defined
		if(!empty($methodParams) && !empty($method)) {
			$params = array_merge($params, $methodParams);
		}

		// API request
		$resp = $this->api->post("/payments", $params);

		// Return payment model
		return new Payment($resp);
	}
}
