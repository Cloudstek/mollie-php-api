<?php

use Mollie\API\Mollie;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Customer payment get tests
 */
class CustomerPaymentGetTest extends ResourceTestCase
{
    /**
     * Get all customer payments
     */
    public function testGetCustomerPayments()
    {
        // Prepare a list of payments
        $paymentListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $payment = $this->getPayment();
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

        // Get payments
        $payments = $api->customer($customerMock->id)->payment()->all();

        // Check the number of payments returned
        $this->assertEquals(count($paymentListMock), count($payments));

        // Check all payments
        $this->assertPayments($payments, $paymentListMock);
    }

    /**
     * Get customer payments without customer ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No customer ID
     */
    public function testGetCustomerPaymentWithoutCustomerID()
    {
        $api = new Mollie('test_testapikey');
        $api->customer()->payment()->all();
    }
}
