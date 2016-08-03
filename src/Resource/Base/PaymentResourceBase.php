<?php

namespace Mollie\API\Resource\Base;

use Mollie\API\Mollie;
use Mollie\API\Model\Payment;
use Mollie\API\Resource\Base\ResourceBase;

abstract class PaymentResourceBase extends ResourceBase
{
    /** @var string Payment ID */
    protected $payment;

    /**
     * Constructor
     *
     * @param Mollie Mollie API reference
     * @param Payment|string $payment
     */
    public function __construct(Mollie $api, $payment = null)
    {
        parent::__construct($api);

        // Store payment ID, if any
        if (isset($payment)) {
            $this->payment = $this->_getPaymentID($payment);
        }
    }

    /**
     * Get payment ID
     *
     * For example:
     * <code>
     * <?php
     * 		$mollie = new Mollie('api_key');
     * 		$customer = $mollie->payment('tr_test')->get()	// call using global defined customer
     * 		$customer = $mollie->payment()->get('tr__test')	// call using local defined customer
     *		$customer = $mollie->payment()->get() 			// Error! No global or local customer defined
     * ?>
     * </code>
     *
     * @param Payment|string $payment
     * @throws InvalidArgumentException
     * @return string
     */
    protected function _getPaymentID($payment)
    {
        return $this->_getResourceID($payment, Payment::class, $this->payment);
    }
}
