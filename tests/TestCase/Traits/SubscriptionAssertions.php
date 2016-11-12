<?php

namespace Mollie\API\Tests\TestCase\Traits;

use Mollie\API\Mollie;
use Mollie\API\Model;

/**
 * Subscription assertions
 */
trait SubscriptionAssertions
{
    /**
     * Get mocked subscription
     * @return object Subscription response object
     */
    protected function getSubscription()
    {
        // TODO Add startDate
        return (object) [
            "resource" => "subscription",
            "id" => "sub_test",
            "customerId" => "cst_test",
            "mode" => "test",
            "createdDatetime" => "2016-06-01T12:23:34.0Z",
            "status" => "active",
            "amount" => "25.00",
            "times" => 4,
            "interval" => "3 months",
            "description" => "Quarterly payment",
            "method" => null,
            "cancelledDatetime" => null,
            "links" => (object) [
                "webhookUrl" => "https://example.org/payments/webhook"
            ]
        ];
    }

    /**
     * Check customer subscription object
     *
     * @param Mollie\API\Model\Subscription $subscription
     * @param object $reference
     */
    protected function assertSubscription($subscription, $reference)
    {
        $this->assertInstanceOf(Model\Subscription::class, $subscription);

        // Check subscription details
        $this->assertModel($subscription, $reference, [
            'id',
            'mode',
            'status',
            'amount',
            'times',
            'interval',
            'description',
            'method',
            'customerId',
            'createdDatetime',
            'cancelledDatetime',
            'links'
        ]);
    }

    /**
     * Check multiple subscription objects
     *
     * @param Mollie\API\Model\Subscription[] $subscriptions
     * @param object[] $references Reference object (raw response)
     */
    protected function assertSubscriptions(array $subscriptions, array $references)
    {
        $this->assertModels($subscriptions, $references, [$this, 'assertSubscription']);
    }
}
