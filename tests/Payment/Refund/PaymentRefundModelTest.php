<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Refund;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Payment refund model tests
 */
class PaymentRefundModelTest extends ResourceTestCase
{
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
}
