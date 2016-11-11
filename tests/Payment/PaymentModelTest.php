<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Payment;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Payment model tests
 */
class PaymentModelTest extends ResourceTestCase
{
    /**
     * Check payment page redirection
     *
     * @runInSeparateProcess
     * @requires extension xdebug
     */
    public function testPaymentRedirect()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get payment
        $payment = new Payment($api, $paymentMock);
        $payment->links->paymentUrl = null;

        // Make sure function returns false if no payment URL is set
        $this->assertFalse($payment->gotoPaymentPage());
        $this->assertNotContains("http://example.com/pay", headers_list());

        // Set payment URL
        $payment->links->paymentUrl = "http://example.com/pay";

        // Make sure the function redirects to the correct URL
        $payment->gotoPaymentPage();
        $this->assertContains("Location: http://example.com/pay", xdebug_get_headers());
    }

    /**
     * Get payment method from model
     */
    public function testGetPaymentMethodFromModel()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the method
        $methodMock = $this->getMethod();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("/methods/{$paymentMock->method}"))
            ->will($this->returnValue($methodMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get payment
        $payment = new Payment($api, $paymentMock);

        // Get method
        $method = $payment->method();
        $this->assertMethod($method, $methodMock);
    }

    /**
     * Get payment method from model
     */
    public function testGetPaymentRefundFromModel()
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
        $payment = new Payment($api, $paymentMock);

        // Get refund
        $refund = $payment->refund($refundMock->id)->get();
        $refund2 = $payment->refund()->get($refundMock->id);

        // Check refund
        $this->assertEquals($refund, $refund2);
        $this->assertRefund($refund, $refundMock, $paymentMock);
    }

    /**
     * Test payment statusses
     */
    public function testGetPaymentStatus()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get payment
        $payment = new Payment($api, $paymentMock);

        // Payment statusses and checks
        $statusses = [
            'open' => 'isOpen',
            'cancelled' => 'isCancelled',
            'expired' => 'hasExpired',
            'failed' => 'hasFailed',
            'pending' => 'isPending',
            'paid' => 'isPaid',
            'paidout' => 'isPaidOut',
            'refunded' => 'isRefunded',
            'charged_back' => 'isChargedBack'
        ];

        // Check null status (all false)
        $payment->status = null;

        foreach ($statusses as $status => $cb) {
            $this->assertFalse($payment->$cb());
        }

        // Check all statusses
        foreach ($statusses as $status => $cb) {
            $payment->status = $status;
            $this->assertTrue($payment->$cb());
        }
    }

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
