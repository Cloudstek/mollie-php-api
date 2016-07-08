<?php

namespace Mollie\API\Resource\Customer;

use Mollie\API\Mollie;
use Mollie\API\Resource\ResourceBase;
use Mollie\API\Model\Mandate;

class MandateResource extends ResourceBase {

	/**
	 * Get customer mandate
	 * @param Customer|string $customer Customer
	 * @param string $id Mandate ID
	 * @return Mandate
	 */
	public function get($customer, $id) {

		// Convert customer argument to ID
		$customer_id = $this->_getCustomerID($customer);

		$resp = $this->api->get("/customers/{$customer_id}/mandates/{$id}");

		// Return mandate model
		return new Mandate($resp);
	}

	/**
	 * Get all customer mandates
	 * @param Customer|string $customer Customer
	 * @return Generator|Mandate[]
	 */
	public function all($customer) {

		// Convert customer argument to ID
		$customer_id = $this->_getCustomerID($customer);

		$items = $this->api->getAll("/customers/{$customer_id}/mandates");

		foreach($items as $item) {
			yield new Mandate($item);
		}
	}
}
