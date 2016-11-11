<?php

namespace Mollie\API\Model;

use Mollie\API\Model\Base\ModelBase;

/**
 * Subscription model
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Subscription extends ModelBase
{
    /** @var string Subscription ID */
    public $id;

    /** @var string Mode used to create this subscription (test or live) */
    public $mode;

    /** @var string Subscription's current status */
    public $status;

    /** @var double Constant amount that is charged with each subscription payment */
    public $amount;

    /** @var int|null Total number of charges for the subscription to complete */
    public $times;

    /** @var string Interval between charges in days, weeks or months */
    public $interval;

    /** @var string Description that along with the charge date in Y-m-d format that will be included in the payment description  */
    public $description;

    /** @var string Payment method used for this subscription. Either forced on creation or null if any of the customer's valid mandates may be used */
    public $method;

    /** @var string Customer ID */
    public $customerId;

    /** @var \DateTime Subscription start date */
    public $startDate;

    /** @var \DateTime Subscription's date and time of creation */
    public $createdDatetime;

    /** @var \DateTime Subscription's date and time of cancellation */
    public $cancelledDatetime;

    /** @var object URLs important to the payment process */
    public $links;

    /**
     * Customer the subscription belongs to
     * @return Customer
     */
    public function customer()
    {
        return $this->api->customer($this->customerId)->get();
    }
}
