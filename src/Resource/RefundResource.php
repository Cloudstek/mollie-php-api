<?php

namespace Mollie\API\Resource;

use Mollie\API\Mollie;
use Mollie\API\Model\Payment;
use Mollie\API\Model\Refund;

class RefundResource extends ResourceBase {

	/**
	 * Get payment refund
	 * @param Payment|string $payment Payment object or payment ID
	 * @param string $refund_id Refund ID
	 * @return Model\Refund
	 */
	public function get($payment, $refund_id) {

		// Convert payment argument to ID
		$payment_id = $this->_getPaymentID($payment);

		$resp = $this->api->get("/payments/{$payment_id}/refunds/{$refund_id}");

		// Return payment model
		return new Refund($resp);
	}

	/**
	 * Get all payments
	 * @return Generator|Refund[]
	 */
	public function all($payment) {

		// Convert payment argument to ID
		$payment_id = $this->_getPaymentID($payment);

		// API request
		$items = $this->api->getAll("/payments/{$payment_id}/refunds");

		// Yield items
		foreach($items as $item) {
			yield new Refund($item);
		}
	}

	/**
	 * Create payment refund
	 * @see https://www.mollie.com/nl/docs/reference/refunds/create
	 * @param Payment|string $payment Payment object or id to refund
	 * @param double $amount The amount in EURO that you want to refund. Omit to refund full amount
	 * @return Refund
	 */
	public function create($payment, $amount = null) {

		// Convert payment argument to ID
		$payment_id = $this->_getPaymentID($payment);

		// API request
		$resp = $this->api->post("/payments", [
			'amount'		=> $amount
		]);

		// Return payment model
		return new Refund($resp);
	}

	/**
	 * Cancel payment refund
	 * @see https://www.mollie.com/nl/docs/reference/refunds/delete
	 * @param Payment|string $payment Payment object or id to cancel refund for
	 * @param int $refund_id Refund ID
	 * @return null
	 */
	public function cancel($payment, $refund_id) {

		// Convert payment argument to ID
		$payment_id = $this->_getPaymentID($payment);

		// API request
		$this->api->delete("/payments/{$payment_id}/refunds/{$refund_id}");
	}
}
