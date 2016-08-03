<?php

use Mollie\API\Tests\ResourceTestCase;
use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Customer;
use Mollie\API\Model\Mandate;

class ResourceTest extends ResourceTestCase
{
    /**
     * Get resource by model
     */
    public function testGetResourceByModel()
    {
        // Customer data
        $customerData = (object) ['id' => 'cst_test'];

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("/customers/cst_test"))
            ->will($this->returnValue($customerData));

        // Initialize API
        $api = new Mollie('test_testapikey', null, $requestMock);

        // Mock the customer
        $customer = new Customer($api, $customerData);

        // Get the customer
        $customer = $api->customer($customer)->get();

        // Assert that the customer ID's are equal
        $this->assertEquals($customer->id, $customerData->id);
    }

    /**
     * Get resource by using an invalid model
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Customer argument must
     */
    public function testGetResourceByInvalidModel()
    {
        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('get')
            ->with($this->equalTo("/customers/mdt_test"));

        // Initialize API
        $api = new Mollie('test_testapikey', null, $requestMock);

        // Mock the mandate
        $mandate = new Mandate($api, ['id' => 'mdt_test']);

        // Get the customer with a mandate object (invalid)
        $customer = $api->customer($mandate)->get();
    }

    /**
     * Get customer resource without supplying resource ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No mandate ID
     */
    public function testGetCustomerResourceWithoutID()
    {
        $api = new Mollie('test_testapikey');
        $api->customer('cst_test')->mandate()->get();
    }

    /**
     * Get customer resource without supplying customer ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No customer ID
     */
    public function testGetCustomerResourceWithoutCustomerID()
    {
        $api = new Mollie('test_testapikey');
        $api->customer()->mandate('mdt_test')->get();
    }

    /**
     * Get customer resource without supplying anything (doh!)
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No customer ID
     */
    public function testGetCustomerResourceWithoutAnything()
    {
        $api = new Mollie('test_testapikey');
        $api->customer()->mandate()->get();
    }
}
