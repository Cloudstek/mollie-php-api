<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Payment refund cancellation tests
 */
class PaymentRefundCancelTest extends ResourceTestCase
{
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
}
