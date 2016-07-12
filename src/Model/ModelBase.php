<?php

namespace Mollie\API\Model;

abstract class ModelBase {

	/**
	 * @var object
	 */
	protected $data;

	/**
	 * Constructor
	 * @param mixed $data
	 */
	public function __construct($data = null) {
		if(isset($data)) {
			$this->fill($data);
		}
	}

	/**
	 * Fill model with data
	 * @param array|object $data
	 * @throws InvalidArgumentException
	 */
	public function fill($data) {
		if(!is_object($data) && !is_array($data)) {
			throw new InvalidArgumentException("Model data should either be an object or an array");
		}

		// Cast to object if required
		if(is_array($data)) {
			$data = json_decode(json_encode($data), false);
		}

		// Set data
		$this->data = $data;
	}

	/**
	 * Magic get method for direct access to model data
	 * @see http://php.net/manual/en/language.oop5.overloading.php#object.get
	 * @return mixed
	 */
	public function __get($name) {
		$value = $this->data->$name;

		if(preg_match('/.+(Datetime|Date)$/', $name)) {
			try {
				$value = new \DateTime($this->data->$name);
			} catch(\Exception $ex) {}
		}

		return $this->data->$name;
	}

	/**
	 * Magic isset method for direct access to model data
	 * @see http://php.net/manual/en/language.oop5.overloading.php#object.isset
	 * @return bool
	 */
	public function __isset($name) {
		return isset($this->data->$name);
	}
}
