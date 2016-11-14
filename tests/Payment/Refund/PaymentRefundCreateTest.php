<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Payment refund creation tests
 */
class PaymentRefundCreateTest extends ResourceTestCase
{
    /**
     * Create payment refund
     */
    public function testCreatePaymentRefund()
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
}
