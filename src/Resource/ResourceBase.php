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
