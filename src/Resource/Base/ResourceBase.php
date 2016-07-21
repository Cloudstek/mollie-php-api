<?php

namespace Mollie\API\Resource\Base;

use Mollie\API\Mollie;

abstract class ResourceBase {

	/** @var Mollie */
	protected $api;

	/** @var string */
	protected $id;

	/**
	 * Constructor
	 *
	 * @param Mollie $api Mollie API reference
	 * @param string $id Resource ID
	 */
	public function __construct(Mollie $api, $id = null) {
		$this->api = $api;
		$this->id = $id;
	}

	/**
	 * Get resource ID
	 *
	 * For example:
	 * <code>
	 * <?php
	 * 		$mollie = new Mollie('api_key');
	 * 		$customer = $mollie->method('tr_test')->get()	// call using global defined customer
	 * 		$customer = $mollie->method()->get('tr__test')	// call using local defined customer
	 *		$customer = $mollie->method()->get() 			// Error! No global or local customer defined
	 * ?>
	 * </code>
	 *
	 * @param string $id
	 * @throws InvalidArgumentException
	 * @return string
	 */
	protected function _getResourceID($id) {
		if(empty($id) && empty($this->id)) {
			throw new \BadMethodCallException("No resource ID was given");
		}

		return empty($id) ? $this->id : $id;
	}
}
