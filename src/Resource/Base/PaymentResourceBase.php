<?php

namespace Mollie\API\Resource\Base;

use Mollie\API\Mollie;
use Mollie\API\Model\Payment;
use Mollie\API\Model\Refund;
use Mollie\API\Resource\Base\ResourceBase;

abstract class PaymentResourceBase extends ResourceBase
{
    /** @var string Payment ID */
    protected $payment;

    /** @var string Refund ID */
    protected $refund;

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
        $this->payment = isset($payment) ? $this->_getPaymentID($payment) : null;
    }

    /**
     * Get payment ID
     *
     * For example:
     * <code>
     * <?php
     *      $mollie = new Mollie('api_key');
     *      $customer = $mollie->payment('tr_test')->get()  // call using global defined customer
     *      $customer = $mollie->payment()->get('tr__test') // call using local defined customer
     *      $customer = $mollie->payment()->get()           // Error! No global or local customer defined
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

    /**
     * Get payment refund ID
     *
     * For example:
     * <code>
     * <?php
     *      $mollie = new Mollie('api_key');
     *      $refund = $mollie->payment('tr_test')->refund('re_test')->get()  // call using global defined refund
     *      $refund = $mollie->payment('tr_test')->refund()->get('re__test') // call using local defined refund
     *      $refund = $mollie->payment('tr_test')->refund()->get()           // Error! No global or local refund defined
     * ?>
     * </code>
     *
     * @param Refund|string $refund
     * @throws InvalidArgumentException
     * @return string
     */
    protected function _getRefundID($refund)
    {
        return $this->_getResourceID($refund, Refund::class, $this->refund);
    }
}
