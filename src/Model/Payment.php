<?php

namespace Mollie\API\Model;

class Payment extends ModelBase {

	/**
	 * Payment statusses
	 * @var array
	 */
	public static $statusses = ['open', 'cancelled', 'pending', 'expired', 'failed', 'paid', 'paidout', 'refunded', 'charged_back'];

	/**
	 * Payment methods
	 * @var array
	 */
	public static $methods = ['ideal', 'creditcard', 'mistercash', 'sofort', 'banktransfer', 'directdebit', 'belfius', 'paypal', 'bitcoin', 'podiumcadeaukaart', 'paysafecard'];

	/**
	 * Magic methods for payment statusses
	 * Called when calling magic methods e.g. isOpen, isCancelled, isChargedBack.
	 * @see http://php.net/manual/en/language.oop5.overloading.php#object.call
	 */
	public function __call($name, $args) {

		// Convert statusses to PascalCase
		$statusNames = array_map(function($status) {
			return ucwords($status, '_');
		}, self::statusses);

		// Payment status (isOpen isCancelled ...)
		if(substr($name, 0, 2) == "is" && in_array(substr($name, 2), $statusNames)) {
			return $this->data->status == strtolower(substr($name, 2));
		}
	}
}
