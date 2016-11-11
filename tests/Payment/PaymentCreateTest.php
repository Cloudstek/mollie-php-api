<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Payment creation tests
 */
class PaymentCreateTest extends ResourceTestCase
{
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
            $paymentMock->metadata,
            [
                'webhookUrl' => $paymentMock->links->webhookUrl,
                'method' => $paymentMock->method,
                'issuer' => $issuerMock->id
            ]
        );

        // Check if we have the correct payment
        $this->assertPayment($payment, $paymentMock);
    }

    /**
     * Create payment with invalid metadata
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Metadata argument must be of type
     */
    public function testCreatePaymentWithInvalidMetadata()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post')
            ->with($this->equalTo("/payments"));

        // Set request handler
        $api->request = $requestMock;

        // Create payment
        $api->payment()->create(
            $paymentMock->amount,
            $paymentMock->description,
            $paymentMock->links->redirectUrl,
            'my invalid metadata :)'
        );
    }
}
