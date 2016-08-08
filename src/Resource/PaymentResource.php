<?php

namespace Mollie\API\Resource;

use Mollie\API\Model\Payment;
use Mollie\API\Resource\Payment\RefundResource;

class PaymentResource extends Base\PaymentResourceBase
{
    /**
     * Get payment
     *
     * @param string $id Payment ID
     * @return Payment
     */
    public function get($id = null)
    {
        // Get payment ID
        $id = $this->_getPaymentID($id);

        // API request
        $resp = $this->api->request->get("/payments/{$id}");

        // Return payment model
        return new Payment($this->api, $resp);
    }

    /**
     * Get all payments
     * @return Generator|Payment[]
     */
    public function all()
    {
        $items = [];

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
     * @param string $webhookUrl Use this parameter to set a webhook URL for this payment only.
     * @param string $method Payment method to use, leave blank to use payment method selection screen
     * @param array $methodParams Payment method specific parameters
     * @param array $metadata Metadata for this payment
     * @param string $recurringType
     * @return Payment
     */
    public function create($amount, $description, $redirectUrl, $webhookUrl = null, $method = null, array $methodParams = null, array $metadata = null, $recurringType = null)
    {
        // Check recurring type
        if (!empty($recurringType) && $recurringType != "first" && $recurringType != "recurring") {
            throw new \InvalidArgumentException("Invalid recurring type '{$recurringType}'. Recurring type must be 'first' or 'recurring'.");
        }

        // Convert metadata to JSON
        $metadata = !empty($metadata) ? json_encode($metadata) : null;

        // Construct parameters
        $params = [
            'amount'        => $amount,
            'description'   => $description,
            'redirectUrl'   => $redirectUrl,
            'webhookUrl'    => $webhookUrl,
            'method'        => $method,
            'metadata'      => $metadata,
            'locale'        => $this->api->getLocale(),
            'recurringType' => $recurringType
        ];

        // Append method parameters if defined
        if (!empty($methodParams) && !empty($method)) {
            $params = array_merge($params, $methodParams);
        }

        // API request
        $resp = $this->api->request->post("/payments", $params);

        // Return payment model
        return new Payment($this->api, $resp);
    }

    /**
     * Payment refund resource
     *
     * @param Mollie\API\Model\Refund|string $refund
     * @return RefundResource
     */
    public function refund($refund = null)
    {
        if (empty($this->payment)) {
            throw new \BadMethodCallException("No payment ID was given");
        }

        return new RefundResource($this->api, $this->payment, $refund);
    }
}
