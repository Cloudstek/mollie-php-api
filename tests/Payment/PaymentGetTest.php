<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Payment get tests
 */
class PaymentGetTest extends ResourceTestCase
{
    /**
     * Get payment
     */
    public function testGetPayment()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo("/payments/{$paymentMock->id}"))
            ->will($this->returnValue($paymentMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get payment
        $payment = $api->payment($paymentMock->id)->get();
        $payment2 = $api->payment()->get($paymentMock->id);

        // Check if we have the correct payment
        $this->assertEquals($payment, $payment2);
        $this->assertPayment($payment, $paymentMock);
    }

    /**
     * Get all payments
     */
    public function testGetPayments()
    {
        // Prepare a list of payments
        $paymentListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $payment = $this->getPayment();

            $payment->id .= "_{$i}";   // tr_test_1
            $payment->description .= "Order {$i}"; // Order 1
            $payment->metadata = [
                "order_id" => $i
            ];

            $paymentListMock[] = $payment;
        }

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $paymentListMock, '/payments');

        // Set request handler
        $api->request = $requestMock;

        // Get payments
        $payments = $api->payment()->all();

        // Check the number of payments returned
        $this->assertEquals(count($paymentListMock), count($payments));

        // Check all payments
        $this->assertPayments($payments, $paymentListMock);
    }

    /**
     * Get payment without ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No payment ID
     */
    public function testGetPaymentWithoutID()
    {
        $api = new Mollie('test_testapikey');
        $api->payment()->get();
    }
}
