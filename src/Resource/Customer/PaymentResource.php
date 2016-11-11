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
     * @param array|object $metadata Metadata for this payment
     * @param array $opts
     *                  [webhookUrl]    string Webhook URL for this payment only
     *                  [method]        string Payment method
     *                  [recurringType] string Recurring payment type, first or recurring
     *                  [mandateId]     string Mandate ID to indicate which of the customers accounts should be credited
     *                  ... Payment method specific options (see documentation)
     * @return Payment
     */
    public function create($amount, $description, $redirectUrl, $metadata = null, array $opts = [])
    {
        // Check recurring type
        if (!empty($opts['recurringType']) && !in_array($opts['recurringType'], ['first', 'recurring'])) {
            throw new \InvalidArgumentException(sprintf("Invalid recurring type '%s'. Recurring type must be 'first' or 'recurring'.", $opts['recurringType']));
        }

        // Check metadata type
        if (!is_object($metadata) && !is_array($metadata)) {
            throw new \InvalidArgumentException('Metadata argument must be of type array or object.');
        }

        // Construct parameters
        $params = [
            'amount'        => $amount,
            'description'   => $description,
            'redirectUrl'   => $redirectUrl,
            'metadata'      => $metadata,
            'locale'        => $this->api->getLocale(),
        ];

        // Merge options
        $params = array_merge($params, $opts);

        // Create payment
        $resp = $this->api->request->post("/customers/{$this->customer}/payments", $params);

        // Return payment model
        return new Payment($this->api, $resp);
    }

    /**
     * Create mandate for recurring payments
     *
     * This is essentially the same as calling create() with the optional parameter 'recurringType' set to 'recurring'.
     * A valid mandate is required to make the recurring payment.
     *
     * @see \Mollie\API\Resource\Customer\MandateResource::create
     * @see https://www.mollie.com/nl/docs/reference/customers/create-payment
     * @param double $amount The amount in EURO that you want to charge
     * @param string $description The description of the payment you're creating
     * @param string $redirectUrl The URL the consumer will be redirected to after the payment process
     * @param array|object $metadata Metadata for this payment
     * @param array $opts
     *                  [webhookUrl]    string  Webhook URL for this payment onlyb
     *                  [method]        string  Payment method
     *                  [recurringType] string  Recurring payment type, first or recurring
     * @return Payment
     */
    public function createFirstRecurring($amount, $description, $redirectUrl, $metadata = nul, array $opts = [])
    {
        // Set recurring type to recurring
        $opts['recurringType'] = 'first';

        // Create recurring payment
        return $this->create($amount, $description, $redirectUrl, $metadata, $opts);
    }

    /**
     * Create recurring payment for customer
     *
     * This is essentially the same as calling create() with the optional parameter 'recurringType' set to 'recurring'.
     * A valid mandate is required to make the recurring payment.
     *
     * @see \Mollie\API\Resource\Customer\MandateResource::create
     * @see https://www.mollie.com/nl/docs/reference/customers/create-payment
     * @param double $amount The amount in EURO that you want to charge
     * @param string $description The description of the payment you're creating
     * @param array|object $metadata Metadata for this payment
     * @param array $opts
     *                  [webhookUrl]    string Webhook URL for this payment only
     *                  [method]        string Payment method
     *                  [recurringType] string Recurring payment type, first or recurring
     *                  [mandateId]     string Mandate ID to indicate which of the customers accounts should be credited
     * @return Payment
     */
    public function createRecurring($amount, $description, $metadata = null, array $opts = [])
    {
        // Set recurring type to recurring
        $opts['recurringType'] = 'recurring';

        // Create recurring payment
        return $this->create($amount, $description, null, $metadata, $opts);
    }
}
