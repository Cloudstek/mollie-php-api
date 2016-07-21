<?php

namespace Mollie\API\Resource\Payment;

use Mollie\API\Mollie;
use Mollie\API\Resource\Base\PaymentResourceBase;
use Mollie\API\Model\Payment;
use Mollie\API\Model\Refund;

class RefundResource extends PaymentResourceBase
{
    /**
     * Get payment refund
     *
     * @param string $refund_id Refund ID
     * @param Payment|string $payment
     * @return Model\Refund
     */
    public function get($refund_id, $payment = null)
    {
        // Get payment ID
        $payment_id = $this->_getPaymentID($payment);

        $resp = $this->api->request->get("/payments/{$payment_id}/refunds/{$refund_id}");

        // Return payment model
        return new Refund($resp);
    }

    /**
     * Get all payment refunds
     * @return Generator|Refund[]
     */
    public function all($payment = null)
    {
        // Get payment ID
        $payment_id = $this->_getPaymentID($payment);

        // API request
        $items = $this->api->request->getAll("/payments/{$payment_id}/refunds");

        // Yield items
        foreach ($items as $item) {
            yield new Refund($item);
        }
    }

    /**
     * Create payment refund
     *
     * @see https://www.mollie.com/nl/docs/reference/refunds/create
     * @param double $amount The amount in EURO that you want to refund. Omit to refund full amount
     * @param Payment|string $payment Payment object or id to refund
     * @return Refund
     */
    public function create($amount = null, $payment = null)
    {
        // Get payment ID
        $payment_id = $this->_getPaymentID($payment);

        // API request
        $resp = $this->api->request->post("/payments", [
            'amount'        => $amount
        ]);

        // Return payment model
        return new Refund($resp);
    }

    /**
     * Cancel payment refund
     *
     * @see https://www.mollie.com/nl/docs/reference/refunds/delete
     * @param int $refund_id Refund ID
     * @param Payment|string $payment Payment object or id to cancel refund for
     * @return null
     */
    public function cancel($refund_id, $payment = null)
    {
        // Get payment ID
        $payment_id = $this->_getPaymentID($payment);

        // API request
        $this->api->request->delete("/payments/{$payment_id}/refunds/{$refund_id}");
    }
}
