<?php

namespace Mollie\API\Tests\TestCase\Traits;

use Mollie\API\Mollie;
use Mollie\API\Model;

/**
 * Payment assertions
 */
trait PaymentAssertions
{
    /**
     * Get mocked payment
     * @return object Payment response object
     */
    protected function getPayment()
    {
        return (object) [
            "id" => "tr_test",
            "mode" => "test",
            "createdDatetime" => "2016-08-01T10:57:45.0Z",
            "status" => "paid",
            "paidDatetime" => "2016-08-01T11:02:28.0Z",
            "amount" => 35.07,
            "description" => "Order 33",
            "method" => "ideal",
            "metadata" => (object) [
                "order_id" => "33"
            ],
            "details" => (object) [
                "consumerName" => "John Doe",
                "consumerAccount" => "NL53INGB0000000000",
                "consumerBic" => "INGBNL2A"
            ],
            'expiryPeriod' => 'P2D',
            "locale" => "nl",
            "profileId" => "pfl_test",
            "links" => (object) [
                "webhookUrl" => "https://webshop.example.org/payments/webhook",
                "redirectUrl" => "https://webshop.example.org/order/33/"
            ]
        ];
    }

    /**
     * Check payment object
     *
     * @param Mollie\API\Model\Payment $payment
     * @param object $reference
     */
    protected function assertPayment($payment, $reference)
    {
        $this->assertInstanceOf(Model\Payment::class, $payment);

        // Check payment details
        $this->assertModel($payment, $reference, [
            'id',
            'mode',
            'status',
            'description',
            'metadata',
            'locale',
            'links',
            'amount',
            'amountRefunded',
            'amountRemaining',
            'createdDatetime',
            'paidDatetime',
            'cancelledDatetime',
            'expiredDatetime',
            'expiryPeriod',
            'method',
            'details',
            'profileId',
            'settlementId'
        ]);
    }

    /**
     * Check multiple payment objects
     *
     * @param Mollie\API\Model\Payment[] $payments
     * @param object[] $references Reference object (raw response)
     */
    protected function assertPayments(array $payments, array $references)
    {
        $this->assertModels($payments, $references, [$this, 'assertPayment']);
    }
}
