<?php

namespace Mollie\API\Model;

use Mollie\API\Base\ModelBase;
use Mollie\API\Resource\Customer\MandateResource;
use Mollie\API\Resource\Customer\PaymentResource;
use Mollie\API\Resource\Customer\SubscriptionResource;

class Customer extends ModelBase {

    /** @var string Customer ID */
    public $id;

    /** @var string API mode (test or live) */
    public $mode;

    /** @var string Customer name */
    public $name;

    /** @var string Customer email */
    public $email;

    /** @var string Customer locale */
    public $locale;

    /** @var object Metadata */
    public $metadata;

    /** @var string[] Payment methods that the customer recently used for payments */
    public $recentlyUsedMethods;

    /** @var \DateTime Customer creation date and time */
    public $createdDatetime;

    /**
     * Customer Mandates
     * @return MandateResource
     */
    public function mandate() {
        return new MandateResource($this->api, $this);
    }

    /**
     * Customer Payments
     * @return PaymentResource
     */
    public function payment() {
        return new PaymentResource($this->api, $this);
    }

    /**
     * Customer Subscriptions
     * @return SubscriptionResource
     */
    public function subscription() {
        return new SubscriptionResource($this->api, $this);
    }
}
