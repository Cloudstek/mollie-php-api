<?php

namespace Mollie\API\Resource\Customer;

use Mollie\API\Mollie;
use Mollie\API\Resource\Base\CustomerResourceBase;
use Mollie\API\Model\Subscription;

class SubscriptionResource extends CustomerResourceBase
{
    /**
     * Subscription resource constructor
     *
     * @param Mollie $api API reference
     * @param Customer|string $customer
     * @param Subscription|string $subscription
     */
    public function __construct(Mollie $api, $customer, $subscription = null)
    {
        parent::__construct($api, $customer);

        if (isset($subscription)) {
            $this->subscription = $this->_getSubscriptionID($subscription);
        }
    }

    /**
     * Get customer subscription
     *
     * @param Subscription|string $id Subscription ID
     * @return Subscription
     */
    public function get($id = null)
    {
        // Get subscription ID
        $subscription_id = $this->_getSubscriptionID($id);

        // Get subscription
        $resp = $this->api->request->get("/customers/{$this->customer}/subscriptions/{$subscription_id}");

        // Return subscription model
        return new Subscription($this->api, $resp);
    }

    /**
     * Get all customer subscriptions
     * @return Subscription[]
     */
    public function all()
    {
        $items = [];

        // Get all subscriptions
        $resp = $this->api->request->getAll("/customers/{$this->customer}/subscriptions");

        if (!empty($resp) && is_array($resp)) {
            foreach ($resp as $item) {
                $items[] = new Subscription($this->api, $item);
            }
        }

        return $items;
    }

    /**
     * Create customer subscription
     *
     * @see https://www.mollie.com/nl/docs/reference/subscriptions/create
     * @param double $amount The constant amount in EURO that you want to charge with each subscription payment
     * @param string $interval Interval to wait between charges like "1 month(s)" or "14 days"
     * @param string $description A description unique per customer. This will be included in the payment description along with the charge date in Y-m-d format
     * @param int|null $times Total number of charges for the subscription to complete. Leave empty for an on-going subscription
     * @param string|null $method The payment method used for this subscription, either forced on creation or null if any of the customer's valid mandates may be used
     * @param string|null $webhookUrl Use this parameter to set a webhook URL for all subscription payments
     * @throws \InvalidArgumentException
     * @return Subscription
     */
    public function create($amount, $interval, $description, $times = null, $method = null, $webhookUrl = null)
    {
        // Check number of times
        if (isset($times) && $times < 1) {
            throw new \InvalidArgumentException("Invalid number of charges for this subscription. Please enter a number of 1 or more, or leave null for an ongoing subscription.");
        }

        // Create customer subscription
        $resp = $this->api->request->post("/customers/{$this->customer}/subscriptions", [
            'amount'        => $amount,
            'times'         => $times,
            'interval'      => $interval,
            'description'   => $description,
            'method'        => $method,
            'webhookUrl'    => $webhookUrl
        ]);

        // Return subscription model
        return new Subscription($this->api, $resp);
    }

    /**
     * Cancel customer subscription
     *
     * @see https://www.mollie.com/nl/docs/reference/subscriptions/delete
     * @param Subscription|string $id Subscription ID
     * @return Subscription
     */
    public function cancel($id = null)
    {
        // Get subscription ID
        $subscription_id = $this->_getSubscriptionID($id);

        // API request
        $resp = $this->api->request->delete("/customers/{$this->customer}/subscriptions/{$subscription_id}");

        // Return cancelled subscription model
        return new Subscription($this->api, $resp);
    }
}
