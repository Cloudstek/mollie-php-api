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
            $this->subscription = $this->getSubscriptionID($subscription);
        }
    }

    /**
     * Get customer subscription
     *
     * @param Subscription|string $subscriptionId Subscription ID
     * @return Subscription
     */
    public function get($subscriptionId = null)
    {
        // Get subscription ID
        $subscriptionId = $this->getSubscriptionID($subscriptionId);

        // Get subscription
        $resp = $this->api->request->get("/customers/{$this->customer}/subscriptions/{$subscriptionId}");

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
     * @param array $opts
     *                  [webhookUrl]    string Use this parameter to set a webhook URL for all subscription payments.
     *                  [method]        string The payment method used for this subscription, creditcard, directdebit or null for any valid mandate
     *                  [startDate]     DateTime The start date of the subscription
     * @throws \InvalidArgumentException
     * @return Subscription
     */
    public function create($amount, $interval, $description, $times = null, array $opts = [])
    {
        // Check number of times
        if (isset($times) && ($times < 1 || !is_numeric($times))) {
            throw new \InvalidArgumentException("Invalid number of charges for this subscription. Please enter a number of 1 or more, or leave null for an ongoing subscription.");
        }

        // Check start date
        if (!empty($opts['startDate'])) {
            if (!($opts['startDate'] instanceof \DateTime)) {
                try {
                    $opts['startDate'] = new \DateTime($opts['startDate']);
                } catch (\Exception $ex) {
                    throw new \InvalidArgumentException("Option startDate must be a valid DateTime object or date string, preferably yyyy-mm-dd.");
                }
            }

            // Format startDate as yyyy-mm-dd
            $opts['startDate'] = $opts['startDate']->format('Y-m-d');
        }

        // Construct parameters
        $params = [
            'amount'        => $amount,
            'times'         => $times,
            'interval'      => $interval,
            'description'   => $description
        ];

        // Merge options
        $params = array_merge($params, $opts);

        // Create customer subscription
        $resp = $this->api->request->post("/customers/{$this->customer}/subscriptions", $params);

        // Return subscription model
        return new Subscription($this->api, $resp);
    }

    /**
     * Cancel customer subscription
     *
     * @see https://www.mollie.com/nl/docs/reference/subscriptions/delete
     * @param Subscription|string $subscriptionId Subscription ID
     * @return Subscription
     */
    public function cancel($subscriptionId = null)
    {
        // Get subscription ID
        $subscriptionId = $this->getSubscriptionID($subscriptionId);

        // API request
        $resp = $this->api->request->delete("/customers/{$this->customer}/subscriptions/{$subscriptionId}");

        // Return cancelled subscription model
        return new Subscription($this->api, $resp);
    }
}
