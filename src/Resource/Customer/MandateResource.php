<?php

namespace Mollie\API\Resource\Customer;

use Mollie\API\Mollie;
use Mollie\API\Model\Customer;
use Mollie\API\Model\Mandate;

class MandateResource extends CustomerResourceBase {

	/**
	 * Get customer mandate
	 * @param string $id Mandate ID
	 * @param Customer|string $customer
	 * @return Mandate
	 */
	public function get($id, $customer = null) {

		// Convert customer argument to ID
		$customer_id = $this->_getCustomerID($customer);

		// API request
		$resp = $this->api->request->get("/customers/{$customer_id}/mandates/{$id}");

		// Return mandate model
		return new Mandate($resp);
	}

	/**
	 * Get all customer mandates
	 * @param Customer|string $customer
	 * @return Generator|Mandate[]
	 */
	public function all($customer = null) {

		// Convert customer argument to ID
		$customer_id = $this->_getCustomerID($customer);

		// API request
		$items = $this->api->request->getAll("/customers/{$customer_id}/mandates");

		// Return mandate model iterator
		foreach($items as $item) {
			yield new Mandate($item);
		}
	}
}
