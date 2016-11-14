<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Customer mandate recocation tests
 */
class CustomerMandateRevokeTest extends ResourceTestCase
{
    /**
     * Revoke a mandate
     */
    public function testRevokeCustomerMandate()
    {
        // Mock the mandate
        $mandateMock = $this->getMandate();

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo("/customers/{$customerMock->id}/mandates/{$mandateMock->id}"));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Revoke mandate
        $api->customer($customerMock->id)->mandate($mandateMock->id)->revoke();
    }
}
