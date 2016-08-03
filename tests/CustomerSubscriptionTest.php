<?php

use Mollie\API\Tests\ResourceTestCase;
use Mollie\API\Mollie;
use Mollie\API\Request;

class CustomerSubscriptionTest extends ResourceTestCase
{
    /** @var object $johnDoeSubscription Subscription for John Doe */
    protected $johnDoeSubscription;

    /**
     * Set Up
     */
    public function setUp()
    {
        // Create subscription for John Doe
        $this->johnDoeSubscription = (object) [
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
     * Get customer subscription
     */
    public function testGetCustomerSubscription()
    {
        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("/customers/cst_test/subscriptions/sub_test"))
            ->will($this->returnValue($this->johnDoeSubscription));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get customer subscription
        $subscription = $api->customer('cst_test')->subscription('sub_test')->get();

        // Check if we have the correct mandate
        $this->assertEquals($this->johnDoeSubscription->id, $subscription->id);
        $this->assertEquals($this->johnDoeSubscription->status, $subscription->status);

        // Check if date objects are parsed correctly
        $this->assertEquals(strtotime($this->johnDoeSubscription->createdDatetime), $subscription->createdDatetime->format('U'));

        // Check links
        $this->assertEquals($this->johnDoeSubscription->links->webhookUrl, $subscription->links->webhookUrl);
    }

    /**
     * Get all customer subscriptions
     */
    public function testGetCustomerSubscriptions()
    {
        // Prepare a list of subscriptions
        $johnDoeSubscriptionList = [];

        for($i = 0; $i <= 15; $i++) {
            $subscription = clone $this->johnDoeSubscription;
            $subscription->id .= "_{$i}";   // sub_test_1

            // Add subscription to list
            $johnDoeSubscriptionList[] = $subscription;
        }

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $johnDoeSubscriptionList, '/customers/cst_test/subscriptions');

        // Set request handler
        $api->request = $requestMock;

        // Get subscriptions
        $subscriptions = $api->customer('cst_test')->subscription()->all();

        // Check the number of subscriptions returned
        $this->assertEquals(count($johnDoeSubscriptionList), count($subscriptions));

        // Get subscriptions through generator
        $subscriptions = [];

        foreach($api->customer('cst_test')->subscription()->yieldAll() as $subscription) {
            $subscriptions[] = $subscription;
        }

        // Check the number of subscriptions returned
        $this->assertEquals(count($johnDoeSubscriptionList), count($subscriptions));
    }

    /**
     * Create customer subscription
     */
    public function testCreateCustomerSubscription()
    {
        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("/customers/cst_test/subscriptions"),
                $this->equalTo([
                    'amount'        => $this->johnDoeSubscription->amount,
                    'times'         => $this->johnDoeSubscription->times,
                    'interval'      => $this->johnDoeSubscription->interval,
                    'description'   => $this->johnDoeSubscription->description,
                    'method'        => $this->johnDoeSubscription->method,
                    'webhookUrl'    => $this->johnDoeSubscription->links->webhookUrl
                ])
            )
            ->will($this->returnValue($this->johnDoeSubscription));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Create subscription
        $subscription = $api->customer('cst_test')->subscription()->create(
            $this->johnDoeSubscription->amount,
            $this->johnDoeSubscription->interval,
            $this->johnDoeSubscription->description,
            $this->johnDoeSubscription->times,
            $this->johnDoeSubscription->method,
            $this->johnDoeSubscription->links->webhookUrl
        );

        // Check subscription details
        $this->assertEquals($this->johnDoeSubscription->id, $subscription->id);
        $this->assertEquals($this->johnDoeSubscription->mode, $subscription->mode);
        $this->assertEquals($this->johnDoeSubscription->status, $subscription->status);
        $this->assertEquals($this->johnDoeSubscription->amount, $subscription->amount);
        $this->assertEquals('double', gettype($subscription->amount));

        // Check if date objects are parsed correctly
        $this->assertEquals(strtotime($this->johnDoeSubscription->createdDatetime), $subscription->createdDatetime->format('U'));
    }

    /**
     * Create customer subscription with invalid number of charges
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid number of charges
     */
    public function testCreateCustomerSubscriptionInvalidtimes()
    {
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
            $this->johnDoeSubscription->amount,
            $this->johnDoeSubscription->interval,
            $this->johnDoeSubscription->description,
            0,
            $this->johnDoeSubscription->method,
            $this->johnDoeSubscription->links->webhookUrl
        );
    }

    /**
     * Cancel customer subscription
     */
    public function testCancelCustomerSubscription()
    {
        // Cancelled subscription mock
        $cancelledSubscription = clone $this->johnDoeSubscription;
        $cancelledSubscription->status = "cancelled";
        $cancelledSubscription->cancelledDatetime = "2016-06-05T12:00:34.0Z";

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo("/customers/cst_test/subscriptions/sub_test"))
            ->will($this->returnValue($cancelledSubscription));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Cancel subscription
        $subscription = $api->customer('cst_test')->subscription('sub_test')->cancel();

        // Check subscription details
        $this->assertEquals($cancelledSubscription->id, $subscription->id);
        $this->assertEquals($cancelledSubscription->mode, $subscription->mode);
        $this->assertEquals($cancelledSubscription->status, $subscription->status);
        $this->assertEquals($cancelledSubscription->amount, $subscription->amount);
        $this->assertEquals('double', gettype($subscription->amount));

        // Check if date objects are parsed correctly
        $this->assertEquals(strtotime($cancelledSubscription->createdDatetime), $subscription->createdDatetime->format('U'));
        $this->assertEquals(strtotime($cancelledSubscription->cancelledDatetime), $subscription->cancelledDatetime->format('U'));
    }
}
