<?php

namespace Mollie\API\Resource\Base;

use Mollie\API\Mollie;
use Mollie\API\Model\Customer;
use Mollie\API\Resource\Base\ResourceBase;

abstract class CustomerResourceBase extends ResourceBase
{
    /** @var string Customer ID */
    protected $customer;

    /**
     * Constructor
     *
     * @param Mollie Mollie API reference
     * @param Customer|string $customer
     */
    public function __construct(Mollie $api, $customer = null)
    {
        parent::__construct($api);

        // Store customer ID, if any
        if (isset($customer)) {
            $this->customer = $this->_getCustomerID($customer);
        }
    }

    /**
     * Get customer ID from string or customer object
     *
     * For example:
     * <code>
     * <?php
     * 		$mollie = new Mollie('api_key');
     * 		$customer = $mollie->customer('cst_test')->get()	// call using global defined customer
     * 		$customer = $mollie->customer()->get('cst_test')	// call using local defined customer
     *		$customer = $mollie->customer()->get() 				// Error! No global or local customer defined
     * ?>
     * </code>
     * @param Customer|string $customer
     * @throws \InvalidArgumentException
     * @return string
     */
    protected function _getCustomerID($customer = null)
    {
        $customer_id = null;

        if ($customer instanceof Customer) {
            $customer_id = $customer->id;
        } elseif (is_string($customer)) {
            $customer_id = $customer;
        } elseif (!empty($customer)) {
            throw new \InvalidArgumentException("Customer argument must either be a Customer object or a string.");
        }

        // If customer argument is empty, check global customer or throw exception when both empty
        if (empty($customer_id)) {
            if (empty($this->customer)) {
                throw new \BadMethodCallException("No customer ID was given");
            }

            return $this->customer;
        }

        return $customer_id;
    }
}
