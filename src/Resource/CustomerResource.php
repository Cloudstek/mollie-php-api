<?php

namespace Mollie\API\Resource;

use Mollie\API\Mollie;
use Mollie\API\Model\Customer;

class CustomerResource extends ResourceBase {

	/**
	 * @var Customer\PaymentResource
	 */
	public $payment;

	/**
	 * @var Customer\MandateResource
	 */
	public $mandate;

	/**
	 * Constructor
	 * @param string $api_key Mollie API key
	 */
	public function __construct(Mollie $api) {

		// Customer resources
		$this->payment 		= new Customer\PaymentResource($api, $this);
		$this->mandate 		= new Customer\MandateResource($api, $this);

		parent::__construct($api);
	}

	/**
	 * Get customer
	 * @param string $id Customer ID
	 * @return Customer
	 */
	public function get($id) {
		$resp = $this->api->get("/customers/{$id}");

		// Return customer model
		return new Customer($resp);
	}

	/**
	 * Get all customers
	 * @return Generator|Customer[]
	 */
	public function all() {
		$items = $this->api->getAll("/customers");

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
		$resp = $this->api->post("/customers", $params);

		// Return customer model
		return new Customer($resp);
	}
}
