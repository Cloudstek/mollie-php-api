<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Customer;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Customer get tests
 */
class CustomerGetTest extends ResourceTestCase
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
