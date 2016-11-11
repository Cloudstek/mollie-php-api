<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Customer subscription cancellation tests
 */
class CustomerSubscriptionCancelTest extends ResourceTestCase
{
    /**
     * Cancel customer subscription
     */
    public function testCancelCustomerSubscription()
    {
        // Cancelled subscription mock
        $cancelledSubscription = $this->getSubscription();
        $cancelledSubscription->status = "cancelled";
        $cancelledSubscription->cancelledDatetime = "2016-06-05T12:00:34.0Z";

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo("/customers/{$customerMock->id}/subscriptions/{$cancelledSubscription->id}"))
            ->will($this->returnValue($cancelledSubscription));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Cancel subscription
        $subscription = $api->customer($customerMock->id)->subscription($cancelledSubscription->id)->cancel();

        // Check subscription details
        $this->assertSubscription($subscription, $cancelledSubscription);
    }
}
