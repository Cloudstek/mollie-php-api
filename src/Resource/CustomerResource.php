<?php

namespace Mollie\API\Resource;

use Mollie\API\Mollie;
use Mollie\API\Model\Customer;

class CustomerResource extends Customer\CustomerResourceBase {

	/**
	 * @var Customer\PaymentResource
	 */
	public $payment;

	/**
	 * @var Customer\MandateResource
	 */
	public $mandate;

	/**
	 * @var Customer\SubscriptionResource
	 */
	public $subscription;

	/**
	 * Constructor
	 * @param string $api_key Mollie API key
	 * @param Customer|string $customer
	 */
	public function __construct(Mollie $api, $customer = null) {

		// Customer resources
		if(isset($customer)) {
			$this->payment		= new Customer\PaymentResource($api, $customer);
			$this->mandate		= new Customer\MandateResource($api, $customer);
			$this->subscription	= new Customer\SubscriptionResource($api, $customer);
		}

		parent::__construct($api, $customer);
	}

	/**
	 * Get customer
	 * @param Customer|string $id
	 * @return Customer
	 */
	public function get($id = null) {

		// Convert customer argument to ID
		$customer_id = $this->_getCustomerID($id);

		// API request
		$resp = $this->api->request->get("/customers/{$id}");

		// Return customer model
		return new Customer($resp);
	}

	/**
	 * Get all customers
	 * @return Generator|Customer[]
	 */
	public function all() {
		$items = $this->api->request->getAll("/customers");

		foreach($items as $item) {
			yield new Customer($item);
		}
	}

	/**
	 * Create customer
	 * @see https://www.mollie.com/nl/docs/reference/customers/create
	 * @param string $name Customer name
	 * @param string $email Customer email
	 * @param string $locale Allow you to preset the language to be used in the payment screens shown to the consumer.
	 * @param array $metadata Metadata for this customer
	 * @return Customer
	 */
	public function create($name, $email, $locale = null, array $metadata = null) {

		// Check locale
		if(!empty($locale)) {
			if(!in_array($locale, Mollie::locales)) {
				$locale = null; // Use browser language
			}
		}

		// Convert metadata to JSON
		if(!empty($metadata)) {
			$metadata = json_encode($metadata);
		}

		// Construct parameters
		$params = [
			'name'		=> $name,
			'email'		=> $email,
			'locale'	=> $locale,
			'metadata'	=> $metadata,
		];

		// API request
		$resp = $this->api->request->post("/customers", $params);

		// Return customer model
		return new Customer($resp);
	}
}
