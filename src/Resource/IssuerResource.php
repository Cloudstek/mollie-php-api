<?php

namespace Mollie\API\Resource;

use Mollie\API\Mollie;
use Mollie\API\Mollie\Model\Issuer;

class IssuerResource extends ResourceBase {

	/**
	 * Get iDEAL issuer
	 * @param string $id iDEAL issuer ID
	 * @return Issuer
	 */
	public function get($id) {
		$resp = $this->api->get("/issuers/{$id}");

		// Return method model
		return new Issuer($resp);
	}

	/**
	 * Get all iDEAL issuers
	 * @return Generator|Issuer[]
	 */
	public function all() {
		$items = $this->api->getAll("/issuers");

		foreach($items as $item) {
			yield new Issuer($item);
		}
	}
}
