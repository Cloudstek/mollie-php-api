<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Payment;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class PaymentTest extends ResourceTestCase
{
    /**
     * Get payment
     */
    public function testGetPayment()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo("/payments/{$paymentMock->id}"))
            ->will($this->returnValue($paymentMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get payment
        $payment = $api->payment($paymentMock->id)->get();
        $payment2 = $api->payment()->get($paymentMock->id);

        // Check if we have the correct payment
        $this->assertEquals($payment, $payment2);
        $this->assertPayment($payment, $paymentMock);
    }

    /**
     * Get all payments
     */
    public function testGetPayments()
    {
        // Prepare a list of payments
        $paymentListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $payment = $this->getPayment();

            $payment->id .= "_{$i}";   // tr_test_1
            $payment->description .= "Order {$i}"; // Order 1
            $payment->metadata = json_encode([
                "order_id" => $i
            ]);

            $paymentListMock[] = $payment;
        }

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $paymentListMock, '/payments');

        // Set request handler
        $api->request = $requestMock;

        // Get payments
        $payments = $api->payment()->all();

        // Check the number of payments returned
        $this->assertEquals(count($paymentListMock), count($payments));
    }

    /**
     * Create payment
     */
    public function testCreatePayment()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the issuer
        $issuerMock = $this->getIssuer();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("/payments"),
                $this->equalTo([
                    'amount'        => $paymentMock->amount,
                    'description'   => $paymentMock->description,
                    'redirectUrl'   => $paymentMock->links->redirectUrl,
                    'webhookUrl'    => $paymentMock->links->webhookUrl,
                    'method'        => $paymentMock->method,
                    'metadata'      => $paymentMock->metadata,
                    'locale'        => $api->getLocale(),
                    'recurringType' => 'first',
                    'issuer'        => $issuerMock->id
                ])
            )
            ->will($this->returnValue($paymentMock));

        // Set request handler
        $api->request = $requestMock;

        // Create payment
        $payment = $api->payment()->create(
            $paymentMock->amount,
            $paymentMock->description,
            $paymentMock->links->redirectUrl,
            $paymentMock->links->webhookUrl,
            $paymentMock->method,
            ['issuer' => $issuerMock->id],
            json_decode($paymentMock->metadata, true),
            'first'
        );

        // Check if we have the correct payment
        $this->assertPayment($payment, $paymentMock);
    }

    public function testGetPaymentExpiry()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get payment
        $payment = new Payment($api, $paymentMock);
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
     * Create invalid recurring payment
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid recurring type
     */
    public function testCreateInvalidRecurringPayment()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post');

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Create payment
        $payment = $api->payment()->create(
            $paymentMock->amount,
            $paymentMock->description,
            $paymentMock->links->redirectUrl,
            $paymentMock->links->webhookUrl,
            $paymentMock->method,
            null,
            json_decode($paymentMock->metadata, true),
            'woopwoop'  // Invalid recurring parameter!
        );
    }

    /**
     * Get payment without ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No payment ID
     */
    public function testGetPaymentWithoutID()
    {
        $api = new Mollie('test_testapikey');
        $api->payment()->get();
    }
}
