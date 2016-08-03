<?php

use Mollie\API\Tests\ResourceTestCase;
use Mollie\API\Mollie;
use Mollie\API\Request;

class CustomerPaymentTest extends ResourceTestCase
{
    /** @var object $johnDoePayment */
    protected $johnDoePayment;

    /**
     * Set Up
     */
    public function setUp()
    {
        // Create payment for John Doe
        $this->johnDoePayment = (object) [
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
            "locale" => "nl",
            "profileId" => "pfl_test",
            "links" => (object) [
                "webhookUrl" => "https://webshop.example.org/payments/webhook",
                "redirectUrl" => "https://webshop.example.org/order/33/"
            ]
        ];
    }

    /**
     * Get all customer payments
     */
    public function testGetCustomerPayments()
    {
        // Prepare a list of payments
        $johnDoePaymentList = [];

        for($i = 0; $i <= 15; $i++) {
            $payment = clone $this->johnDoePayment;
            $payment->id .= "_{$i}";   // tr_test_1

            // Add mandate to list
            $johnDoePaymentList[] = $payment;
        }

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $johnDoePaymentList, '/customers/cst_test/payments');

        // Set request handler
        $api->request = $requestMock;

        // Get payments
        $payments = $api->customer('cst_test')->payment()->all();

        // Check the number of payments returned
        $this->assertEquals(count($johnDoePaymentList), count($payments));

        // Get payments through generator
        $payments = [];

        foreach($api->customer('cst_test')->payment()->yieldAll() as $payment) {
            $payments[] = $payment;
        }

        // Check the number of payments returned
        $this->assertEquals(count($johnDoePaymentList), count($payments));
    }

    /**
     * Create customer payment
     */
    public function testCreateCustomerPayment()
    {
        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("/customers/cst_test/payments"),
                $this->equalTo([
                    'amount'        => $this->johnDoePayment->amount,
                    'description'   => $this->johnDoePayment->description,
                    'redirectUrl'   => $this->johnDoePayment->links->redirectUrl,
                    'webhookUrl'    => $this->johnDoePayment->links->webhookUrl,
                    'method'        => $this->johnDoePayment->method,
                    'metadata'      => json_encode($this->johnDoePayment->metadata),
                    'locale'        => null,
                    'recurringType' => 'first',
                    'issuer'        => 'ideal_INGNL2A'
                ])
            )
            ->will($this->returnValue($this->johnDoePayment));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get payment
        $payment = $api->customer('cst_test')->payment()->create(
            $this->johnDoePayment->amount,
            $this->johnDoePayment->description,
            $this->johnDoePayment->links->redirectUrl,
            $this->johnDoePayment->links->webhookUrl,
            $this->johnDoePayment->method,
            ['issuer' => 'ideal_INGNL2A'],
            (array) $this->johnDoePayment->metadata,
             'first'
         );

        // Check if we have the correct customer
        $this->assertEquals($this->johnDoePayment->id, $payment->id);
        $this->assertEquals($this->johnDoePayment->mode, $payment->mode);

        // Check if JSON metadata is correctly parsed
        $this->assertEquals($this->johnDoePayment->metadata, $payment->metadata);

        // Check if date objects are parsed correctly
        $this->assertEquals(strtotime($this->johnDoePayment->createdDatetime), $payment->createdDatetime->format('U'));
        $this->assertEquals(strtotime($this->johnDoePayment->paidDatetime), $payment->paidDatetime->format('U'));

        // Check links
        $this->assertEquals($this->johnDoePayment->links->redirectUrl, $payment->links->redirectUrl);

        // Check details
        $this->assertEquals($this->johnDoePayment->details->consumerName, $payment->details->consumerName);
    }

    /**
     * Create customer payment with invalid recurring type
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid recurring type
     */
    public function testCreateInvalidRecurringCustomerPayment()
    {
        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post');

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get payment
        $payment = $api->customer('cst_test')->payment()->create(
            $this->johnDoePayment->amount,
            $this->johnDoePayment->description,
            $this->johnDoePayment->links->redirectUrl,
            $this->johnDoePayment->links->webhookUrl,
            $this->johnDoePayment->method,
            ['issuer' => 'ideal_INGNL2A'],
            (array) $this->johnDoePayment->metadata,
             $this->johnDoePayment->locale,
             'superawesome' // Invalid recurring type
         );
    }
}
