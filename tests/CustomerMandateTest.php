<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Mandate;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class CustomerMandateTest extends ResourceTestCase
{
    /**
     * Get customer mandate
     */
    public function testGetCustomerMandate()
    {
        // Mock the mandate
        $mandateMock = $this->getMandate();

        // Mock the customer
        $customerMock = $this->getCustomer();

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

        // Get customer mandate
        $mandate = $api->customer($customerMock->id)->mandate($mandateMock->id)->get();
        $mandate2 = $api->customer($customerMock->id)->mandate()->get($mandateMock->id);

        // Check if we have the correct mandate
        $this->assertEquals($mandate, $mandate2);
        $this->assertMandate($mandate, $mandateMock);
    }

    /**
     * Get all customer mandates
     */
    public function testGetCustomerMandates()
    {
        // Prepare a list of mandates for John Doe
        $mandateListMock = [];

        for($i = 0; $i <= 15; $i++) {
            $mandate = $this->getMandate();
            $mandate->id .= "_{$i}";   // mdt_test_1

            // Leave one valid mandate
            if($i < 15) {
                $mandate->status = "invalid";
            }

            // Add mandate to list
            $mandateListMock[] = $mandate;
        }

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $mandateListMock, "/customer/{$customerMock->id}/mandates");

        // Set request handler
        $api->request = $requestMock;

        // Get mandates
        $mandates = $api->customer($customerMock->id)->mandate()->all();

        // Check the number of mandates returned
        $this->assertEquals(count($mandateListMock), count($mandates));
    }

    /**
     * Get customer for mandate object
     */
    public function testGetCustomerForMandate()
    {
        // Mock the mandate
        $mandateMock = $this->getMandate();

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

        // Get customer mandate
        $mandate = new Mandate($api, $mandateMock);

        // Get customer
        $customer = $mandate->customer();
        $this->assertCustomer($customer, $customerMock);
    }

    /**
     * Get customer mandate without mandate ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No mandate ID
     */
    public function testGetCustomerMandateWithoutID()
    {
        $api = new Mollie('test_testapikey');
        $api->customer('cst_test')->mandate()->get();
    }

    /**
     * Get customer mandate without customer ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No customer ID
     */
    public function testGetCustomerMandateWithoutCustomerID()
    {
        $api = new Mollie('test_testapikey');
        $api->customer()->mandate('mdt_test')->get();
    }

    /**
     * Get customer mandate without any arguments (doh!)
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No customer ID
     */
    public function testGetCustomerMandateWithoutAnything()
    {
        $api = new Mollie('test_testapikey');
        $api->customer()->mandate()->get();
    }
}
