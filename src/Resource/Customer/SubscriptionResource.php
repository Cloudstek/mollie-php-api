<?php

namespace Mollie\API\Resource\Customer;

use Mollie\API\Mollie;
use Mollie\API\Base\CustomerResourceBase;
use Mollie\API\Model\Customer;
use Mollie\API\Model\Subscription;

class SubscriptionResource extends CustomerResourceBase {

	/**
	 * Get customer subscription
	 *
	 * @param string $id Subscription ID
	 * @param Customer|string $customer
	 * @return Subscription
	 */
	public function get($id, $customer = null) {

		// Convert customer argument to ID
		$customer_id = $this->_getCustomerID($customer);

		// API request
		$resp = $this->api->request->get("/customers/{$customer_id}/subscriptions/{$id}");

		// Return subscription model
		return new Subscription($resp);
	}

	/**
	 * Get all customer subscriptions
	 *
	 * @param Customer|string $customer
	 * @return Generator|Mandate[]
	 */
	public function all($customer = null) {

		// Convert customer argument to ID
		$customer_id = $this->_getCustomerID($customer);

		// API request
		$items = $this->api->request->getAll("/customers/{$customer_id}/subscriptions");

		// Return subscription model iterator
		foreach($items as $item) {
			yield new Subscription($item);
		}
	}

	/**
	 * Create customer subscription
	 *
	 * @see https://www.mollie.com/nl/docs/reference/subscriptions/create
	 * @param double $amount The constant amount in EURO that you want to charge with each subscription payment
	 * @param string $interval Interval to wait between charges like "1 month(s)" or "14 days"
	 * @param string $description A description unique per customer. This will be included in the payment description along with the charge date in Y-m-d format
	 * @param Customer|string $customer
	 * @param int|null $times Total number of charges for the subscription to complete. Leave empty for an on-going subscription
	 * @param string|null $method The payment method used for this subscription, either forced on creation or null if any of the customer's valid mandates may be used
	 * @param string|null $webhookUrl Use this parameter to set a webhook URL for all subscription payments
	 * @throws \InvalidArgumentException
	 * @return Subscription
	 */
	public function create($amount, $interval, $description, $customer = null, $times = null, $method = null, $webhookUrl = null) {

		// Convert customer argument to ID
		$customer_id = $this->_getCustomerID($customer);

		// Check number of times
		if(isset($times) && (!is_integer($times) || $times < 1)) {
			throw new \InvalidArgumentException("Invalid number of charges for this subscription. Please enter a number of 1 or more, or leave null for an ongoing subscription.");
		}

		// Check payment method
		if(!empty($method)) {
			if(!in_array($method, ['creditcard', 'directdebit'])) {
				throw new \InvalidArgumentException("Invalid payment method '{$method}'. Please see https://www.mollie.com/nl/docs/reference/subscriptions/create for available payment methods.");
			}
		}

		// Construct parameters
		$params = [
			'amount'		=> $amount,
			'times'			=> $times,
			'interval'		=> $interval,
			'description'	=> $description,
			'method'		=> $method,
			'webhookUrl'	=> $webhookUrl
		];

		// API request
		$resp = $this->api->request->post("/customers/{$customer_id}/subscriptions", $params);

		// Return subscription model
		return new Subscription($resp);
	}

	/**
	 * Cancel customer subscription
	 * 
	 * @see https://www.mollie.com/nl/docs/reference/subscriptions/delete
	 * @param string $id Subscription ID
	 * @param Customer|string $customer
	 * @return Subscription
	 */
	public function cancel($id, $customer = null) {

		// Convert customer argument to ID
		$customer_id = $this->_getCustomerID($customer);

		// API request
		$resp = $this->api->request->delete("/customers/{$customer_id}/subscriptions/{$id}");

		// Return cancelled subscription model
		return new Subscription($resp);
	}
}
