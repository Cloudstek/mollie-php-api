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
	 * @param object|array $data
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
				$this->$k = $this->_parseDates($k, $v);
			} else {
				throw new \ModelException("Unexpected property '{$k}' in response.", $this);
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
	 * @return object
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * Parse dates into their respective objects (DateTime or DateInterval)
	 *
	 * @param string $name Variable name
	 * @return DateTime|DateInterval
	 */
	protected function _parseDates($name, $value) {
		if(!empty($value) && is_string($value)) {

			// ISO 8601 Date
			if(preg_match('/.+(Datetime|Date)$/', $name)) {
				try {
				 	return new \DateTime($value);
				} catch(\Exception $ex) {
					throw new \ModelException("Property {$name} does not contain a valid ISO 8601 date/time string.", $this);
				}
			}

			// ISO 8601 Duration
			if(preg_match('/.+(Period)$/', $name) && preg_match('^P.+', $value)) {
				try {
					return new \DateInterval($value);
				} catch(\Exception $ex) {
					throw new \ModelException("Property {$name} does not contain a valid ISO 8601 duration string.", $this);
				}
			}
		}

		return $value;
	}
}
