<?php

namespace Mollie\API\Model;

class Mandate extends Base\ModelBase
{
    /** @var string Mandate ID */
    public $id;

    /** @var string Mandate status */
    public $status;

    /** @var string Mandate payment method */
    public $method;

    /** @var string Mandate customer ID */
    public $customerId;

    /**
     * @var object Mandate details that are different per payment method
     * @see https://www.mollie.com/nl/docs/reference/mandates/get
     */
    public $details;

    /** @var \DateTime Mandate creation date and time */
    public $createdDatetime;

    /**
     * Mandate customer
     * @return Customer
     */
    public function customer()
    {
        return $this->api->customer($this->customerId)->get();
    }
}
