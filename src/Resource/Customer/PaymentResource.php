<?php

namespace Mollie\API\Resource\Customer;

use Mollie\API\Mollie;
use Mollie\API\Model\Customer;
use Mollie\API\Model\Payment;

class PaymentResource extends CustomerResourceBase {

	/**
	 * Get all customer payments
	 * @param Customer|string $customer
	 * @return Generator|Payment[]
	 */
	public function all($customer = null) {

		// Convert customer argument to ID
		$customer_id = $this->_getCustomerID($customer);

		// API request
		$items = $this->api->request->getAll("/customers/{$customer_id}/payments");

		// Return payment model iterator
		foreach($items as $item) {
			yield new Payment($item);
		}
	}

	/**
	 * Create customer payment
	 * @see https://www.mollie.com/nl/docs/reference/payments/create
	 * @see https://www.mollie.com/nl/docs/reference/customers/create-payment
	 * @param double $amount The amount in EURO that you want to charge
	 * @param string $description The description of the payment you're creating.
	 * @param string $redirectUrl The URL the consumer will be redirected to after the payment process.
	 * @param Customer|string $customer
	 * @param string $webhookUrl Use this parameter to set a webhook URL for this payment only.
	 * @param string $method Payment method to use, leave blank to use payment method selection screen
	 * @param array $methodParams Payment method specific parameters
	 * @param array $metadata Metadata for this payment
	 * @param string $locale Allow you to preset the language to be used in the payment screens shown to the consumer.
	 * @param string $recurringType
	 * @throws \InvalidArgumentException
	 * @return Payment
	 */
	public function create($amount, $description, $redirectUrl, $customer = null, $webhookUrl = null, $method = null, array $methodParams = null, array $metadata = null, $locale = null, $recurringType = null) {

		// Convert customer argument to ID
		$customer_id = $this->_getCustomerID($customer);

		// Check payment method
		if(!empty($method)) {
			if(!in_array($method, Payment::methods)) {
				throw new \InvalidArgumentException("Invalid payment method '{$method}'. Please see https://www.mollie.com/nl/docs/reference/payments/create for available payment methods.");
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
				throw new \InvalidArgumentException("Invalid recurring type '{$recurringType}'. Recurring type must be 'first' or 'recurring'.");
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
		$resp = $this->api->request->post("/customers/{$customer_id}/payments", $params);

		// Return payment model
		return new Payment($resp);
	}
}
