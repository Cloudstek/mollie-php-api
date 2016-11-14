<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Customer payment creation tests
 */
class CustomerPaymentCreateTest extends ResourceTestCase
{
    /**
     * Create customer payment
     */
    public function testCreateCustomerPayment()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("/customers/{$customerMock->id}/payments"),
                $this->equalTo([
                    'amount'        => $paymentMock->amount,
                    'description'   => $paymentMock->description,
                    'redirectUrl'   => $paymentMock->links->redirectUrl,
                    'webhookUrl'    => $paymentMock->links->webhookUrl,
                    'method'        => $paymentMock->method,
                    'metadata'      => $paymentMock->metadata,
                    'locale'        => $api->getLocale(),
                    'issuer'        => 'ideal_INGNL2A'
                ])
            )
            ->will($this->returnValue($paymentMock));

        // Set request handler
        $api->request = $requestMock;

        // Get payment
        $payment = $api->customer($customerMock->id)->payment()->create(
            $paymentMock->amount,
            $paymentMock->description,
            $paymentMock->links->redirectUrl,
            $paymentMock->metadata,
            [
                'webhookUrl' => $paymentMock->links->webhookUrl,
                'method' => $paymentMock->method,
                'issuer' => 'ideal_INGNL2A',
            ]
        );

        // Check if we have the correct customer
        $this->assertPayment($payment, $paymentMock);
    }

    /**
     * Create recurring customer payments
     */
    public function testCreateRecurringCustomerPayment()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->exactly(2))
            ->method('post')
            ->withConsecutive(
                [
                    $this->equalTo("/customers/{$customerMock->id}/payments"),
                    [
                        'amount'        => $paymentMock->amount,
                        'description'   => $paymentMock->description,
                        'redirectUrl'   => $paymentMock->links->redirectUrl,
                        'webhookUrl'    => $paymentMock->links->webhookUrl,
                        'method'        => $paymentMock->method,
                        'metadata'      => $paymentMock->metadata,
                        'locale'        => $api->getLocale(),
                        'issuer'        => 'ideal_INGNL2A',
                        'recurringType' => 'first'
                    ]
                ],
                [
                    $this->equalTo("/customers/{$customerMock->id}/payments"),
                    [
                        'amount'        => $paymentMock->amount,
                        'description'   => $paymentMock->description,
                        'redirectUrl'   => null,
                        'webhookUrl'    => $paymentMock->links->webhookUrl,
                        'method'        => $paymentMock->method,
                        'metadata'      => $paymentMock->metadata,
                        'locale'        => $api->getLocale(),
                        'issuer'        => 'ideal_INGNL2A',
                        'recurringType' => 'recurring'
                    ]
                ]
            )
            ->will($this->returnValue($paymentMock));

        // Set request handler
        $api->request = $requestMock;

        // Create first recurring payment
        $firstPayment = $api->customer($customerMock->id)->payment()->createFirstRecurring(
            $paymentMock->amount,
            $paymentMock->description,
            $paymentMock->links->redirectUrl,
            $paymentMock->metadata,
            [
                'webhookUrl' => $paymentMock->links->webhookUrl,
                'method' => $paymentMock->method,
                'issuer' => 'ideal_INGNL2A',
            ]
        );

        // Create recurring payment
        $secondPayment = $api->customer($customerMock->id)->payment()->createRecurring(
            $paymentMock->amount,
            $paymentMock->description,
            $paymentMock->metadata,
            [
                'webhookUrl' => $paymentMock->links->webhookUrl,
                'method' => $paymentMock->method,
                'issuer' => 'ideal_INGNL2A',
            ]
        );

        // Check if we have the correct customer
        $this->assertPayment($firstPayment, $paymentMock);
        $this->assertPayment($secondPayment, $paymentMock);
    }

    /**
     * Create customer payment with invalid recurring type
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid recurring type
     */
    public function testCreateInvalidRecurringCustomerPayment()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post');

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get payment
        $payment = $api->customer($customerMock->id)->payment()->create(
            $paymentMock->amount,
            $paymentMock->description,
            $paymentMock->links->redirectUrl,
            $paymentMock->metadata,
            [
                'webhookUrl' => $paymentMock->links->webhookUrl,
                'method' => $paymentMock->method,
                'issuer' => 'ideal_INGNL2A',
                'recurringType' => 'superawesome' // Invalid recurring type
            ]
        );
    }

    /**
     * Create customer payment with invalid metadata
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Metadata argument must be of type
     */
    public function testCreateCustomerPaymentWithInvalidMetadata()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post')
            ->with($this->equalTo("/customers/{$customerMock->id}/payments"));

        // Set request handler
        $api->request = $requestMock;

        // Create payment
        $api->customer($customerMock->id)->payment()->create(
            $paymentMock->amount,
            $paymentMock->description,
            $paymentMock->links->redirectUrl,
            'my funny coment and invalid metadata.'
        );
    }
}
