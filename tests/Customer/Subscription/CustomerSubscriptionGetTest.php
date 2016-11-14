<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Customer subscription get tests
 */
class CustomerSubscriptionGetTest extends ResourceTestCase
{
    /**
     * Get customer subscription
     */
    public function testGetCustomerSubscription()
    {
        // Mock the subscription
        $subscriptionMock = $this->getSubscription();

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo("/customers/{$customerMock->id}/subscriptions/{$subscriptionMock->id}"))
            ->will($this->returnValue($subscriptionMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get customer subscription
        $subscription = $api->customer($customerMock->id)->subscription($subscriptionMock->id)->get();
        $subscription2 = $api->customer($customerMock->id)->subscription()->get($subscriptionMock->id);

        // Check if we have the correct subscription
        $this->assertEquals($subscription, $subscription2);
        $this->assertSubscription($subscription, $subscriptionMock);
    }

    /**
     * Get all customer subscriptions
     */
    public function testGetCustomerSubscriptions()
    {
        // Prepare a list of subscriptions
        $subscriptionListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $subscription = $this->getSubscription();
            $subscription->id .= "_{$i}";   // sub_test_1

            // Add subscription to list
            $subscriptionListMock[] = $subscription;
        }

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $subscriptionListMock, "/customers/{$customerMock->id}/subscriptions");

        // Set request handler
        $api->request = $requestMock;

        // Get subscriptions
        $subscriptions = $api->customer($customerMock->id)->subscription()->all();

        // Check the number of subscriptions returned
        $this->assertEquals(count($subscriptionListMock), count($subscriptions));

        // Check all subscriptions
        $this->assertSubscriptions($subscriptions, $subscriptionListMock);
    }

    /**
     * Get customer subscription without subscription ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No subscription ID
     */
    public function testGetCustomerSubscriptionWithoutID()
    {
        $api = new Mollie('test_testapikey');
        $api->customer('cst_test')->subscription()->get();
    }

    /**
     * Get customer subscription without customer ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No customer ID
     */
    public function testGetCustomerSubscriptionWithoutCustomerID()
    {
        $api = new Mollie('test_testapikey');
        $api->customer()->subscription('sub_test')->get();
    }

    /**
     * Get customer subscription without any arguments (doh!)
     *
     * @expectedException BadMethodCallException
     */
    public function testGetCustomeSubscriptionWithoutAnything()
    {
        $api = new Mollie('test_testapikey');
        $api->customer()->subscription()->get();
    }
}
