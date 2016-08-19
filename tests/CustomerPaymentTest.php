<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class CustomerPaymentTest extends ResourceTestCase
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
    }

    /**
     * Create customer payment
     */
    public function testCreateCustomerPayment()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("/customers/{$customerMock->id}/payments"),
                $this->equalTo([
                    'amount'        => $paymentMock->amount,
                    'description'   => $paymentMock->description,
                    'redirectUrl'   => $paymentMock->links->redirectUrl,
                    'webhookUrl'    => $paymentMock->links->webhookUrl,
                    'method'        => $paymentMock->method,
                    'metadata'      => $paymentMock->metadata,
                    'locale'        => $api->getLocale(),
                    'recurringType' => 'first',
                    'issuer'        => 'ideal_INGNL2A'
                ])
            )
            ->will($this->returnValue($paymentMock));

        // Set request handler
        $api->request = $requestMock;

        // Get payment
        $payment = $api->customer($customerMock->id)->payment()->create(
            $paymentMock->amount,
            $paymentMock->description,
            $paymentMock->links->redirectUrl,
            json_decode($paymentMock->metadata, true),
            [
                'webhookUrl' => $paymentMock->links->webhookUrl,
                'method' => $paymentMock->method,
                'methodParams' => ['issuer' => 'ideal_INGNL2A'],
                'recurringType' => 'first'
            ]
         );

        // Check if we have the correct customer
        $this->assertPayment($payment, $paymentMock);
    }

    /**
     * Create customer payment with invalid recurring type
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid recurring type
     */
    public function testCreateInvalidRecurringCustomerPayment()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post');

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get payment
        $payment = $api->customer('cst_test')->payment()->create(
            $paymentMock->amount,
            $paymentMock->description,
            $paymentMock->links->redirectUrl,
            json_decode($paymentMock->metadata, true),
            [
                'webhookUrl' => $paymentMock->links->webhookUrl,
                'method' => $paymentMock->method,
                'methodParams' => ['issuer' => 'ideal_INGNL2A'],
                'recurringType' => 'superawesome' // Invalid recurring type
            ]
         );
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
