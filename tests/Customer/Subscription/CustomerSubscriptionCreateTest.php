<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Customer subscription creation tests
 */
class CustomerSubscriptionCreateTest extends ResourceTestCase
{
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
                    'webhookUrl'    => $subscriptionMock->links->webhookUrl,
                    'startDate'     => $subscriptionMock->startDate
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
            [
                'method'        => $subscriptionMock->method,
                'webhookUrl'    => $subscriptionMock->links->webhookUrl,
                'startDate'     => $subscriptionMock->startDate
            ]
        );

        // Check subscription details
        $this->assertSubscription($subscription, $subscriptionMock);
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

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post');

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Create subscription
        $subscription = $api->customer($customerMock->id)->subscription()->create(
            $subscriptionMock->amount,
            $subscriptionMock->interval,
            $subscriptionMock->description,
            0,
            [
                'method'        => $subscriptionMock->method,
                'webhookUrl'    => $subscriptionMock->links->webhookUrl,
                'startDate'     => $subscriptionMock->startDate
            ]
        );
    }

    /**
     * Create customer subscription with invalid start date (yyyy-mm-dd format)
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Option startDate must be a valid DateTime object or date string
     */
    public function testCreateCustomerSubscriptionInvalidStartDate()
    {
        // Mock the subscription
        $subscriptionMock = $this->getSubscription();

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post');

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Create subscription
        $subscription = $api->customer($customerMock->id)->subscription()->create(
            $subscriptionMock->amount,
            $subscriptionMock->interval,
            $subscriptionMock->description,
            null,
            [
                'method'        => $subscriptionMock->method,
                'webhookUrl'    => $subscriptionMock->links->webhookUrl,
                'startDate'     => 'blabla'
            ]
        );
    }
}
