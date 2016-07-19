<?php

namespace Mollie\API\Exception;

class ModelException extends \Exception {

	/** @var ModelBase */
	private $model;

	/**
	 * Model exception constructor
	 *
	 * @param string $message
	 * @param ModelBase $model
	 * @param int $code
	 */
	public function __construct($message, $model, $code = 0) {
		$this->model = $model;

		$model_name = get_class($model);
		$message = "[{$model_name}] {$message}";

		parent::__construct($message, $code);
	}

	/**
	 * Get model that threw the exception
	 *
	 * @return ModelBase
	 */
	public function getModel() {
		return $this->model;
	}
}
