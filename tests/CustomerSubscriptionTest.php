<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Subscription;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class CustomerSubscriptionTest extends ResourceTestCase
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

        for($i = 0; $i <= 15; $i++) {
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
    }

    /**
     * Create customer subscription
     */
    public function testCreateCustomerSubscription()
    {
        // Mock the subscription
        $subscriptionMock = $this->getSubscription();

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("/customers/{$customerMock->id}/subscriptions"),
                $this->equalTo([
                    'amount'        => $subscriptionMock->amount,
                    'times'         => $subscriptionMock->times,
                    'interval'      => $subscriptionMock->interval,
                    'description'   => $subscriptionMock->description,
                    'method'        => $subscriptionMock->method,
                    'webhookUrl'    => $subscriptionMock->links->webhookUrl
                ])
            )
            ->will($this->returnValue($subscriptionMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Create subscription
        $subscription = $api->customer($customerMock->id)->subscription()->create(
            $subscriptionMock->amount,
            $subscriptionMock->interval,
            $subscriptionMock->description,
            $subscriptionMock->times,
            $subscriptionMock->method,
            $subscriptionMock->links->webhookUrl
        );

        // Check subscription details
        $this->assertSubscription($subscription, $subscriptionMock);
    }

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

    /**
     * Create customer subscription with invalid number of charges
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid number of charges
     */
    public function testCreateCustomerSubscriptionInvalidtimes()
    {
        // Mock the subscription
        $subscriptionMock = $this->getSubscription();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post');

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Create subscription
        $subscription = $api->customer('cst_test')->subscription()->create(
            $subscriptionMock->amount,
            $subscriptionMock->interval,
            $subscriptionMock->description,
            0,
            $subscriptionMock->method,
            $subscriptionMock->links->webhookUrl
        );
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
     * @covers Mollie\API\Resource\CustomerResource::payment
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
     * @expectedExceptionMessage No customer ID
     */
    public function testGetCustomeSubscriptionWithoutAnything()
    {
        $api = new Mollie('test_testapikey');
        $api->customer()->subscription()->get();
    }
}
