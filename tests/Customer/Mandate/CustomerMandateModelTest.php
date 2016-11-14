<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Mandate;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Customer mandate model tests
 */
class CustomerMandateModelTest extends ResourceTestCase
{
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
     * Get mandate status
     */
    public function testCustomerMandateStatus()
    {
        // Mock the mandate
        $mandateMock = $this->getMandate();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get mandate
        $mandate = new Mandate($api, $mandateMock);

        // Check valid status
        $mandate->status = "valid";
        $this->assertFalse($mandate->isInvalid());
        $this->assertTrue($mandate->isValid());

        // Check invalid status
        $mandate->status = "invalid";
        $this->assertTrue($mandate->isInvalid());
        $this->assertFalse($mandate->isValid());
    }

    /**
     * Check if a list of mandates has a valid mandate
     */
    public function testCustomerMandateHasValid()
    {
        // Prepare a list of mandates
        $mandateListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $mandate = $this->getMandate("invalid");
            $mandate->id .= "_{$i}";   // mdt_test_1

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

        // Mandate resource
        $customerMandate = $api->customer($customerMock->id)->mandate();

        // Make sure no valid mandates are found
        $this->assertFalse($customerMandate->hasValid());

        // Set a mandate to valid and check again
        $mandateListMock[10]->status = 'valid';
        $this->assertTrue($customerMandate->hasValid());
    }
}
