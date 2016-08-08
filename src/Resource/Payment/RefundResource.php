<?php

namespace Mollie\API\Resource\Payment;

use Mollie\API\Mollie;
use Mollie\API\Resource\Base\PaymentResourceBase;
use Mollie\API\Model\Payment;
use Mollie\API\Model\Refund;

class RefundResource extends PaymentResourceBase
{
    /**
     * Payment refund resource constructor
     *
     * @param Mollie $api API reference
     * @param Payment|string $payment
     * @param Refund|string $refund
     */
    public function __construct(Mollie $api, $payment, $refund = null)
    {
        parent::__construct($api, $payment);

        if (isset($refund)) {
            $this->refund = $this->_getRefundID($refund);
        }
    }

    /**
     * Get payment refund
     *
     * @param Refund|string $id
     * @return Refund
     */
    public function get($id = null)
    {
        // Get refund ID
        $refund_id = $this->_getRefundID($id);

        // Get refund
        $resp = $this->api->request->get("/payments/{$this->payment}/refunds/{$refund_id}");

        // Return refund model
        return new Refund($this->api, $resp);
    }

    /**
     * Get all payment refunds
     * @return Refund[]
     */
    public function all()
    {
        $items = [];

        // API request
        $resp = $this->api->request->getAll("/payments/{$this->payment}/refunds");

        foreach ($resp as $item) {
            $items[] = new Refund($this->api, $item);
        }

        return $items;
    }

    /**
     * Create payment refund
     *
     * @see https://www.mollie.com/nl/docs/reference/refunds/create
     * @param double $amount The amount in EURO that you want to refund. Omit to refund full amount
     * @return Refund
     */
    public function create($amount = null)
    {
        // API request
        $resp = $this->api->request->post("/payments/{$this->payment}/refunds", [
            'amount'        => $amount
        ]);

        // Return refund model
        return new Refund($this->api, $resp);
    }

    /**
     * Cancel payment refund
     *
     * @see https://www.mollie.com/nl/docs/reference/refunds/delete
     * @param Refund|string $id
     */
    public function cancel($id = null)
    {
        // Get refund ID
        $refund_id = $this->_getRefundID($id);

        // API request
        $this->api->request->delete("/payments/{$this->payment}/refunds/{$refund_id}");
    }
}
