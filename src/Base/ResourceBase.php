<?php

namespace Mollie\API\Resource;

use Mollie\API\Mollie;

/**
 * Resource base
 * Base class for all API resources. Defined the default constructor and helper functions to de-duplicate frequently used code.
 */
abstract class ResourceBase {

	/**
	 * @var Mollie
	 */
	protected $api;

	/**
	 * Constructor
	 * @param Mollie Mollie API reference
	 */
	public function __construct(Mollie $api) {
		$this->api = $api;
	}
}
