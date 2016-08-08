<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Refund;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class PaymentRefundTest extends ResourceTestCase
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
            $refund->id += "_{$i}";     // re_test_1

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
    }

    /**
     * Create payment refund
     */
    public function testCreatePayment()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();
        $paymentMock->status = "refunded";
        $paymentMock->amountRefunded = $paymentMock->amount;
        $paymentMock->amountRemaining = 0;

        // Mock the refund
        $refundMock = $this->getRefund($paymentMock);
        $refundMock->amount = $paymentMock->amount;

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("/payments/{$paymentMock->id}/refunds"),
                $this->equalTo([
                    'amount'    => $refundMock->amount
                ])
            )
            ->will($this->returnValue($refundMock));

        // Set request handler
        $api->request = $requestMock;

        // Create refund
        $refund = $api->payment($paymentMock->id)->refund()->create(
            $refundMock->amount
        );

        // Check if we have the correct payment
        $this->assertRefund($refund, $refundMock);
    }

    /**
     * Cancel customer subscription
     */
    public function testCancelPaymentRefund()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the refund
        $refundMock = $this->getRefund($paymentMock);

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->exactly(2))
            ->method('delete')
            ->with($this->equalTo("/payments/{$paymentMock->id}/refunds/{$refundMock->id}"));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Cancel refund
        $api->payment($paymentMock->id)->refund($refundMock->id)->cancel();
        $api->payment($paymentMock->id)->refund()->cancel($refundMock->id);
    }

    /**
     * Get payment from model
     */
    public function testGetPaymentFromModel()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the refund
        $refundMock = $this->getRefund($paymentMock);

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get refund
        $refund = new Refund($api, $refundMock);

        // Get payment
        $payment = $refund->payment();
        $this->assertPayment($payment, $paymentMock);
    }

    /**
     * Get refund without ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No refund ID
     */
    public function testGetRefundWithoutID()
    {
        $api = new Mollie('test_testapikey');
        $api->payment('tr_test')->refund()->get();
    }

    /**
     * Get refund without anything
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No payment ID
     */
    public function testGetRefundWithoutAnything()
    {
        $api = new Mollie('test_testapikey');
        $api->payment()->refund()->get();
    }
}
