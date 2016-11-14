<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Subscription;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Customer subscription model tests
 */
class CustomerSubscriptionModelTest extends ResourceTestCase
{
    /**
     * Get customer for subscription object
     */
    public function testGetCustomerFromModel()
    {
        // Mock the subscription
        $subscriptionMock = $this->getSubscription();

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("/customers/{$customerMock->id}"))
            ->will($this->returnValue($customerMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get customer subscription
        $subscription = new Subscription($api, $subscriptionMock);

        // Get customer
        $customer = $subscription->customer();
        $this->assertCustomer($customer, $customerMock);
    }
}
