<?php

namespace Mollie\API\Resource\Payment;

use Mollie\API\Mollie;
use Mollie\API\Model\Payment;
use Mollie\API\Resource\ResourceBase;

abstract class PaymentResourceBase extends ResourceBase {

	/**
	 * @var string Payment ID
	 */
	protected $payment;

	/**
	 * Constructor
	 * @param Mollie Mollie API reference
	 * @param Payment|string $payment
	 */
	public function __construct(Mollie $api, $payment = null) {
		parent::__construct($api);

		// Store payment ID, if any
		if(isset($payment)) {
			$this->payment = $this->_getPaymentID($payment);
		}
	}

	/**
	 * Get payment ID from string or payment object
	 *
	 * For example:
	 * <code>
	 * <?php
	 * 		$mollie = new Mollie('api_key');
	 * 		$customer = $mollie->payment('tr_test')->get()	// call using global defined customer
	 * 		$customer = $mollie->payment()->get('tr__test')	// call using local defined customer
	 *		$customer = $mollie->payment()->get() 			// Error! No global or local customer defined
	 * ?>
	 * </code>
	 * @param Payment|string $payment
	 * @throws InvalidArgumentException
	 * @return string
	 */
	protected function _getPaymentID($payment) {
		$payment_id = null;

		if($payment instanceof Payment) {
			$payment_id = $payment->id;
		} elseif(is_string($payment)) {
			$payment_id = $payment;
		} elseif(!empty($payment)) {
			throw new \InvalidArgumentException("Payment argument must either be a Payment object or a string.");
		}

		// If payment argument is empty, check global payment or throw exception when both empty
		if(empty($payment_id)) {
			if(empty($this->payment)) {
				throw new \BadMethodCallException("No payment ID was given");
			}

			return $this->payment;
		}

		return $payment_id;
	}
}
