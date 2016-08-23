<?php

use Mollie\API\Mollie;
use Mollie\API\Model\Payment;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class PaymentSettlementTest extends ResourceTestCase
{
    /**
     * Get payment settlement from model
     *
     * @expectedException Exception
     * @expectedExceptionMessage Not implemented
     */
    public function testGetPaymentSettlementFromModel()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get payment
        $payment = new Payment($api, $paymentMock);

        // Get profile
        $payment->settlement();
    }
}
