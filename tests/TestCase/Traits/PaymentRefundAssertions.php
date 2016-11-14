<?php

namespace Mollie\API\Tests\TestCase\Traits;

use Mollie\API\Mollie;
use Mollie\API\Model;

/**
 * Payment refund assertions
 */
trait PaymentRefundAssertions
{
    /**
     * Get mocked refund
     *
     * @param object $payment Payment response object
     * @return object Refund response object
     */
    protected function getRefund($payment = null)
    {
        $payment = isset($payment) ? $payment : $this->getPayment();

        return (object) [
            "id" => "re_test",
            "payment" => (object) $payment,
            "amount" => "5.95",
            "refundedDatetime" => "2016-08-07T15:43:16.0Z"
        ];
    }

    /**
     * Check refund object
     *
     * @param Mollie\API\Model\Refund $refund
     * @param object $reference
     */
    protected function assertRefund($refund, $reference)
    {
        $this->assertInstanceOf(Model\Refund::class, $refund);

        // Check refund details
        $this->assertModel($refund, $reference, [
            'id',
            'amount',
            'status',
            'refundedDatetime'
        ]);

        // Payment
        $this->assertInstanceOf(Model\Payment::class, $refund->payment());
        $this->assertPayment($refund->payment(), $reference->payment);
    }

    /**
     * Check multiple refund objects
     *
     * @param Mollie\API\Model\Refund[] $refunds
     * @param object[] $references Reference object (raw response)
     */
    protected function assertRefunds(array $refunds, array $references)
    {
        $this->assertModels($refunds, $references, [$this, 'assertRefund']);
    }
}
