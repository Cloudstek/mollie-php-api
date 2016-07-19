<?php

namespace Mollie\API\Model;

use Mollie\API\Mollie;

abstract class ModelBase {

	/** @var object */
	protected $response;

	/** @var Mollie */
	protected $api;

	/** @var string Resource type */
	public $resource;

	/**
	 * Constructor
	 *
	 * @param Mollie $api
	 * @param mixed $data
	 */
	public function __construct(Mollie $api, $data = null) {
		if(isset($data)) {
			$this->fill($data);
		}

		// API reference
		$this->api = $api;
	}

	/**
	 * Fill model with data
	 *
	 * @param array|object $data
	 * @throws InvalidArgumentException
	 */
	public function fill($data) {
		if(!is_object($data) && !is_array($data) && !$data instanceof \Traversable) {
			throw new InvalidArgumentException("Model data should either be an object, array or any other traversable object.");
		}

		foreach($data as $k => $v) {
			if(property_exists($this, $k)) {
				$this->$k = $v;
			} else {
				throw new \ModelException("Property {$k} is not a member of this model.", $this);
			}
		}

		// Cast to object if array
		if(is_array($data)) {
			$data = json_decode(json_encode($data), false);
		}

		// Set raw response data
		$this->response = $data;
	}

	/**
	 * Get JSON response object
	 *
	 * @return object
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * Convert values to correct type on assignment
	 *
	 * Used for example to convert DateTime strings to DateTime objects on assignment.
	 */
	public function __set($name, $value) {
		if(property_exists($this, $name)) {

			// DateTime
			if(is_string($value) && preg_match('/.+(Datetime|Date)$/', $name)) {
				try {
					$this->$name = new \DateTime($value);
				} catch(\Exception $ex) {
					throw new \ModelException("Property {$name} is not a valid DateTime object or string as it's name suggests.", $this);
				}
			}

			$this->$name = $value;
		} else {
			throw new \ModelException("Property {$name} is not a member of this model.", $this);
		}
	}
}
