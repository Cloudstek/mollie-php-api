<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Payment refund get tests
 */
class PaymentRefundGetTest extends ResourceTestCase
{
    /**
     * Get payment refund
     */
    public function testGetPaymentRefund()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the refund
        $refundMock = $this->getRefund($paymentMock);

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo("/payments/{$paymentMock->id}/refunds/{$refundMock->id}"))
            ->will($this->returnValue($refundMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get payment
        $refund = $api->payment($paymentMock->id)->refund($refundMock->id)->get();
        $refund2 = $api->payment($paymentMock->id)->refund()->get($refundMock->id);

        // Check if we have the correct refund
        $this->assertEquals($refund, $refund2);
        $this->assertRefund($refund, $refundMock);
    }

    /**
     * Get all payment refunds
     */
    public function testGetPaymentRefunds()
    {
        // Prepare a list of refunds
        $refundListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $refund = $this->getRefund();
            $refund->id .= "_{$i}";     // re_test_1

            $refundListMock[] = $refund;
        }

        // Mock the payment
        $paymentMock = $this->getPayment();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $refundListMock, "/payments/{$paymentMock->id}/refunds");

        // Set request handler
        $api->request = $requestMock;

        // Get refunds
        $refunds = $api->payment($paymentMock->id)->refund()->all();

        // Check the number of refunds returned
        $this->assertEquals(count($refundListMock), count($refunds));

        // Check all refunds
        $this->assertRefunds($refunds, $refundListMock);
    }

    /**
     * Get payment refund without refund ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No refund ID
     */
    public function testGetPaymentRefundWithoutID()
    {
        $api = new Mollie('test_testapikey');
        $api->payment('tr_test')->refund()->get();
    }

    /**
     * Get payment refund without payment ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No payment ID
     */
    public function testGetPaymentRefundWithoutCustomerID()
    {
        $api = new Mollie('test_testapikey');
        $api->payment()->refund('re_test')->get();
    }

    /**
     * Get payment refund without any arguments (doh!)
     *
     * @expectedException BadMethodCallException
     */
    public function testGetPaymentRefundWithoutAnything()
    {
        $api = new Mollie('test_testapikey');
        $api->payment()->refund()->get();
    }
}
