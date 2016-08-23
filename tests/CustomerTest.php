<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Customer;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class CustomerTest extends ResourceTestCase
{
    /**
     * Get customer
     */
    public function testGetCustomer()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo("/customers/{$customerMock->id}"))
            ->will($this->returnValue($customerMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get customer
        $customer = $api->customer($customerMock->id)->get();
        $customer2 = $api->customer()->get($customerMock->id);

        // Check if we have the correct customer
        $this->assertEquals($customer, $customer2);
        $this->assertCustomer($customer, $customerMock);
    }

    /**
     * Get all customers
     */
    public function testGetCustomers()
    {
        // Prepare a list of customers
        $customerListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $customer = $this->getCustomer();

            $customer->id .= "_{$i}";   // cst_test_1
            $customer->name .= " {$i}"; // Customer 1

            $customerListMock[] = $customer;
        }

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $customerListMock, '/customers');

        // Set request handler
        $api->request = $requestMock;

        // Get customers
        $customers = $api->customer()->all();

        // Check the number of customers returned
        $this->assertEquals(count($customerListMock), count($customers));

        // Check all customers
        $this->assertcustomers($customers, $customerListMock);
    }

    /**
     * Create customer
     */
    public function testCreateCustomer()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("/customers"),
                $this->equalTo([
                    'name'      => $customerMock->name,
                    'email'     => $customerMock->email,
                    'locale'    => null,
                    'metadata'  => $customerMock->metadata
                ])
            )
            ->will($this->returnValue($customerMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Create customer
        $customer = $api->customer()->create($customerMock->name, $customerMock->email, json_decode($customerMock->metadata, true));

        // Check if we have the correct customer
        $this->assertCustomer($customer, $customerMock);
    }

    /**
     * Get customer mandate through customer object
     *
     * Will first fetch the customer and then get the specified mandate.
     */
    public function testGetCustomerMandateFromModel()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the mandate
        $mandateMock = $this->getMandate();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo("/customers/{$customerMock->id}/mandates/{$mandateMock->id}"))
            ->will($this->returnValue($mandateMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get customer
        $customer = new Customer($api, $customerMock);

        // Get customer mandate
        $mandate = $customer->mandate($mandateMock->id)->get();
        $mandate2 = $customer->mandate()->get($mandateMock->id);

        // Check if we have the correct mandate
        $this->assertEquals($mandate, $mandate2);
        $this->assertMandate($mandate, $mandateMock);
    }

    /**
     * Get customer payments through customer object
     *
     * Will first fetch the customer and then get all payments by that customer
     */
    public function testGetCustomerPaymentsFromModel()
    {
        // Prepare a list of payments
        $paymentListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $payment = $this->getPayment();
            $payment->id .= "_{$i}";   // tr_test_1

            $paymentListMock[] = $payment;
        }

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $paymentListMock, "/customers/{$customerMock->id}/payments");

        // Set request handler
        $api->request = $requestMock;

        // Get customer
        $customer = new Customer($api, $customerMock);

        // Get customer payments
        $payments = $customer->payment()->all();

        // Check if we have the correct payment
        $this->assertEquals(count($paymentListMock), count($payments));
    }

    /**
     * Get customer subscription through customer object
     *
     * Will first fetch the customer and then get the specified subscription.
     */
    public function testGetCustomerSubscriptionFromModel()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the subscription
        $subscriptionMock = $this->getSubscription();

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

        // Get customer
        $customer = new Customer($api, $customerMock);

        // Get customer subscription
        $subscription = $customer->subscription($subscriptionMock->id)->get();
        $subscription2 = $customer->subscription()->get($subscriptionMock->id);

        // Check if we have the correct subscription
        $this->assertEquals($subscription, $subscription2);
        $this->assertSubscription($subscription, $subscriptionMock);
    }

    /**
     * Get customer without customer ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No customer ID
     */
    public function testGetCustomerWithoutID()
    {
        $api = new Mollie('test_testapikey');
        $api->customer()->get();
    }
}
