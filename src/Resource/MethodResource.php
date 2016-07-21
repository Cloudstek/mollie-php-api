<?php

namespace Mollie\API\Resource;

use Mollie\API\Mollie;
use Mollie\API\Mollie\Model\Method;

class MethodResource extends Base\ResourceBase {

	/**
	 * Get payment method
	 *
	 * @param string|null $id Payment method ID
	 * @return Method
	 */
	public function get($id = null) {

		// Get method ID
		$id = $this->_getResourceID($id);

		$resp = $this->api->request->get("/methods/{$id}");

		// Return method model
		return new Method($resp);
	}

	/**
	 * Get all payment methods
	 * @return Generator|Method[]
	 */
	public function all() {
		$items = $this->api->request->getAll("/methods");

		foreach($items as $item) {
			yield new Method($item);
		}
	}
}
