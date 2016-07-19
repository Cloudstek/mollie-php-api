<?php

namespace Mollie\API\Model;

use Mollie\API\Base\ModelBase;

class Refund extends ModelBase {

	/**
	 * @var Payment
	 */
	private $payment;

	/**
	 * Constructor
	 * @param mixed $data
	 */
	public function __construct($data = null) {
		if(isset($data)) {
			$this->payment = new Payment($data->payment);
			unset($data->payment);
		}

		parent::__construct($data);
	}
}
