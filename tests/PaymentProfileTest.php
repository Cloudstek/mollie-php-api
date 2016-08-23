<?php

use Mollie\API\Mollie;
use Mollie\API\Model\Payment;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class PaymentProfile extends ResourceTestCase
{
    /**
     * Get payment profile from model
     *
     * @expectedException Exception
     * @expectedExceptionMessage Not implemented
     */
    public function testGetPaymentProfile()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get payment
        $payment = new Payment($api, $paymentMock);

        // Get profile
        $payment->profile();
    }
}
