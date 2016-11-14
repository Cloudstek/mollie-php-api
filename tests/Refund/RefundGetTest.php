<?php

use Mollie\API\Mollie;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class RefundGetTest extends ResourceTestCase
{
    /**
     * Get all payments
     */
    public function testGetRefunds()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Prepare a list of refunds
        $refundListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $refund = $this->getRefund($paymentMock);
            $refund->id .= "_{$i}";   // re_test_1

            $refundListMock[] = $refund;
        }

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $refundListMock, '/refunds');

        // Set request handler
        $api->request = $requestMock;

        // Get refunds
        $refunds = $api->refund()->all();

        // Check the number of refunds returned
        $this->assertEquals(count($refundListMock), count($refunds));

        // Check all refunds
        $this->assertRefunds($refunds, $refundListMock);
    }
}
