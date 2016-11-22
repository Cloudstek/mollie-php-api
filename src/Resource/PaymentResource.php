<?php

namespace Mollie\API\Resource;

use Mollie\API\Model\Payment;
use Mollie\API\Model\Refund;
use Mollie\API\Resource\Base\PaymentResourceBase;
use Mollie\API\Resource\Payment\RefundResource as PaymentRefundResource;

class PaymentResource extends PaymentResourceBase
{
    /**
     * Get payment
     *
     * @param string $paymentId Payment ID
     * @return Payment
     */
    public function get($paymentId = null)
    {
        // Get payment ID
        $paymentId = $this->getPaymentID($paymentId);

        // API request
        $resp = $this->api->request->get("/payments/{$paymentId}");

        // Return payment model
        return new Payment($this->api, $resp);
    }

    /**
     * Get all payments
     * @return Generator|Payment[]
     */
    public function all()
    {
        $items = array();

        // Get all payments
        $resp = $this->api->request->getAll("/payments");

        if (!empty($resp) && is_array($resp)) {
            foreach ($resp as $item) {
                $items[] = new Payment($this->api, $item);
            }
        }

        return $items;
    }

    /**
     * Create payment
     *
     * @see https://www.mollie.com/nl/docs/reference/payments/create
     * @param double $amount The amount in EURO that you want to charge
     * @param string $description The description of the payment you're creating.
     * @param string $redirectUrl The URL the consumer will be redirected to after the payment process.
     * @param array|object $metadata Metadata for this payment
     * @param array $opts
     *                  [webhookUrl]    string Webhook URL for this payment only
     *                  [method]        string Payment method
     *                  ... Payment method specific options (see documentation)
     * @return Payment
     */
    public function create($amount, $description, $redirectUrl, $metadata = null, array $opts = array())
    {
        // Check metadata type
        if (!is_object($metadata) && !is_array($metadata)) {
            throw new \InvalidArgumentException('Metadata argument must be of type array or object.');
        }

        // Construct parameters
        $params = array(
            'amount'        => $amount,
            'description'   => $description,
            'redirectUrl'   => $redirectUrl,
            'metadata'      => $metadata,
            'locale'        => $this->api->getLocale()
        );

        // Merge options
        $params = array_merge($params, $opts);

        // API request
        $resp = $this->api->request->post("/payments", $params);

        // Return payment model
        return new Payment($this->api, $resp);
    }

    /**
     * Payment refund resource
     *
     * @param Refund|string $refund
     * @return PaymentRefundResource
     */
    public function refund($refund = null)
    {
        if (empty($this->payment)) {
            throw new \BadMethodCallException("No payment ID was given");
        }

        return new PaymentRefundResource($this->api, $this->payment, $refund);
    }
}
