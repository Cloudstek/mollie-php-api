<?php

namespace Mollie\API\Model;

use Mollie\API\Model\Base\ModelBase;
use Mollie\API\Resource\Payment\RefundResource;

/**
 * Payment model
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Payment extends ModelBase
{
    /** @var string Payment ID */
    public $id;

    /** @var string API mode (test or live) */
    public $mode;

    /** @var string Payment status */
    public $status;

    /** @var string Short description of the payment as shown on the bank or card statement when possible */
    public $description;

    /** @var object Payment metadata */
    public $metadata;

    /** @var string Customer locale */
    public $locale;

    /** @var object Several URLs important to the payment process. */
    public $links;

    /** @var double Payment amount */
    public $amount;

    /** @var double Amount refunded (only available when refunds are available for this payment) */
    public $amountRefunded;

    /** @var double Amount remaining (only available when refunds are available for this payment) */
    public $amountRemaining;

    /** @var \DateTime Payment creation date and time */
    public $createdDatetime;

    /** @var \DateTime Date and time the payment became paid */
    public $paidDatetime;

    /** @var \DateTime Date and time the payment was cancelled */
    public $cancelledDatetime;

    /** @var \DateTime Date and time the payment was expired */
    public $expiredDatetime;

    /** @var \DateInterval Period until the payment will expire */
    public $expiryPeriod;

    /** @var string Payment method */
    public $method;

    /** @var object|null Payment method specific details */
    public $details;

    /** @var string Profile ID */
    public $profileId;

    /** @var string Settlement ID */
    public $settlementId;

    /**
     * Payment status is open
     * @return boolean
     */
    public function isOpen()
    {
        return $this->status === 'open';
    }

    /**
     * Payment status is cancelled
     * @return boolean
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Payment status is expired
     * @return boolean
     */
    public function hasExpired()
    {
        return $this->status === 'expired';
    }

    /**
     * Payment status is failed
     * @return boolean
     */
    public function hasFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Payment status is pending
     * @return boolean
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Payment status is paid
     * @return boolean
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Payment status is paid out
     * @return boolean
     */
    public function isPaidOut()
    {
        return $this->status === 'paidout';
    }

    /**
     * Payment status is refunded
     * @return boolean
     */
    public function isRefunded()
    {
        return $this->status === 'refunded';
    }

    /**
     * Payment status is charged back
     * @return boolean
     */
    public function isChargedBack()
    {
        return $this->status === 'charged_back';
    }

    /**
     * Payment method
     * @return Method
     */
    public function method()
    {
        return $this->api->method($this->method)->get();
    }

    /**
     * Redirect customer to payment page
     * @return boolean Returns false if no payment URL is available
     */
    public function gotoPaymentPage()
    {
        if (!empty($this->links->paymentUrl)) {
            header('Location: ' . $this->links->paymentUrl);
        }

        return false;
    }

    /**
     * Profile the payment was created on
     * @return Profile
     */
    public function profile()
    {
        throw new \Exception('Not implemented.');
    }

    /**
     * Settlement the payment belongs to
     * @return Settlement
     */
    public function settlement()
    {
        throw new \Exception('Not implemented.');
    }

    /**
     * Refunds connected to this payment
     *
     * @param Refund|string $refund Refund ID
     * @return RefundResource
     */
    public function refund($refund = null)
    {
        return new RefundResource($this->api, $this, $refund);
    }
}
