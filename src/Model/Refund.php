<?php

namespace Mollie\API\Model;

class Refund extends Base\ModelBase
{
    /** @var string Refund ID */
    public $id;

    /** @var Payment The original payment related to the refund */
    protected $payment;

    /** @var double The amount refunded to the customer */
    public $amount;

    /** @var string Refund status */
    public $status;

    /** @var \DateTime The date and time the refund was issued */
    public $refundedDatetime;

    /**
     * The original payment related to the refund
     * @return Payment
     */
    public function payment()
    {
        return new Payment($this->api, $this->payment);
    }
}
