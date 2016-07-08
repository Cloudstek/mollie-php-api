<?php

namespace Mollie\API\Resource;

use Mollie\API\Mollie;
use Mollie\API\Model;

/**
 * Resource base
 * Base class for all API resources. Defined the default constructor and helper functions to de-duplicate frequently used code.
 */
abstract class ResourceBase {

	/**
	 * @var Mollie
	 */
	protected $api;

	/**
	 * Constructor
	 * @param Mollie Mollie API reference
	 */
	public function __construct(Mollie $api) {
		$this->api = $api;
	}

	/**
	 * Get customer ID from customer object
	 * @param Model\Customer|string $customer
	 * @throws InvalidArgumentException
	 * @return string
	 */
	protected function _getCustomerID($customer) {
		if($customer instanceof Model\Customer) {
			$customer_id = $customer->id;
		} elseif(is_string($customer)) {
			$customer_id = $customer;
		} else {
			throw new InvalidArgumentException("Customer argument must either be a Customer object or a string.");
		}

		return $customer_id;
	}

	/**
	 * Get payment ID from payment object
	 * @param Payment|string $payment
	 * @throws InvalidArgumentException
	 * @return string
	 */
	protected function _getPaymentID($payment) {
		if($payment instanceof Payment) {
			$payment_id = $payment->id;
		} elseif(is_string($payment)) {
			$payment_id = $payment;
		} else {
			throw new InvalidArgumentException("Payment argument must either be a Payment object or a string.");
		}

		return $payment_id;
	}
}
