<?php

namespace Mollie\API\Resource\Customer;

use Mollie\API\Resource\Base\CustomerResourceBase;
use Mollie\API\Model\Payment;

class PaymentResource extends CustomerResourceBase
{
    /**
     * Get all customer payments
     * @return Payment[]
     */
    public function all()
    {
        $items = [];

        // Get all customer payments
        $resp = $this->api->request->getAll("/customers/{$this->customer}/payments");

        if (!empty($resp) && is_array($resp)) {
            foreach ($resp as $item) {
                $items[] = new Payment($this->api, $item);
            }
        }

        return $items;
    }

    /**
     * Create customer payment
     *
     * @see https://www.mollie.com/nl/docs/reference/customers/create-payment
     * @param double $amount The amount in EURO that you want to charge
     * @param string $description The description of the payment you're creating
     * @param string $redirectUrl The URL the consumer will be redirected to after the payment process
     * @param array $metadata Metadata for this payment
     * @param array $opts
     *                  [webhookUrl]    string Webhook URL for this payment only
     *                  [method]        string Payment method
     *                  [methodParams]  array  Payment method specific options (see documentation)
     *                  [recurringType] string Recurring payment type, first or recurring
     * @return Payment
     */
    public function create($amount, $description, $redirectUrl, array $metadata = [], array $opts = [])
    {
        // Check recurring type
        if (!empty($opts['recurringType']) && $opts['recurringType'] != "first" && $opts['recurringType'] != "recurring") {
            throw new \InvalidArgumentException(sprintf("Invalid recurring type '%s'. Recurring type must be 'first' or 'recurring'.", $opts['recurringType']));
        }

        // Convert metadata to JSON
        $metadata = !empty($metadata) ? json_encode($metadata) : null;

        // Construct parameters
        $params = [
            'amount'        => $amount,
            'description'   => $description,
            'redirectUrl'   => $redirectUrl,
            'webhookUrl'    => $opts['webhookUrl'] ?: null,
            'method'        => $opts['method'] ?: null,
            'metadata'      => $metadata,
            'locale'        => $this->api->getLocale(),
            'recurringType' => $opts['recurringType'] ?: null
        ];

        // Append method parameters if defined
        if (!empty($opts['method']) && !empty($opts['methodParams'])) {
            $params = array_merge($params, $opts['methodParams']);
        }

        // Create payment
        $resp = $this->api->request->post("/customers/{$this->customer}/payments", $params);

        // Return payment model
        return new Payment($this->api, $resp);
    }
}
