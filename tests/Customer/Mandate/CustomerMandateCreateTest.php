<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Customer mandate create tests
 */
class CustomerMandateCreateTest extends ResourceTestCase
{
    /**
     * Create customer mandate
     */
    public function testCreateCustomerMandate()
    {
        // Mock the mandate
        $mandateMock = $this->getCustomerMandate();

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
                $this->equalTo("/customers/{$customerMock->id}/mandates"),
                $this->equalTo([
                    'method' => 'directdebit',
                    'consumerName' => $mandateMock->details->consumerName,
                    'consumerAccount' => $mandateMock->details->consumerAccount,
                    'consumerBic' => $mandateMock->details->consumerBic,
                    'signatureDate' => $mandateMock->details->signatureDate,
                    'mandateReference' => $mandateMock->details->mandateReference
                ])
            )
            ->will($this->returnValue($mandateMock));

        // Set request handler
        $api->request = $requestMock;

        // Create mandate
        $mandate = $api->customer($customerMock->id)->mandate()->create(
            $mandateMock->details->consumerName,
            $mandateMock->details->consumerAccount,
            [
                'consumerBic' => $mandateMock->details->consumerBic,
                'signatureDate' => new \DateTime($mandateMock->details->signatureDate),
                'mandateReference' => $mandateMock->details->mandateReference
            ]
        );

        // Check if we have the correct mandate
        $this->assertMandate($mandate, $mandateMock);
    }

    /**
     * Create customer mandate with invalid signature date
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument signatureDate must be of type DateTime
     */
    public function testCreateCustomerMandateInvalidSignatureDate()
    {
        // Mock the mandate
        $mandateMock = $this->getCustomerMandate();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post');

        // Set request handler
        $api->request = $requestMock;

        // Create mandate
        $mandate = $api->customer('cst_test')->mandate()->create(
            $mandateMock->details->consumerName,
            $mandateMock->details->consumerAccount,
            [
                'bic' => $mandateMock->details->consumerBic,
                'signatureDate' => 'lalala', // Invalid date
                'reference' => $mandateMock->details->mandateReference
            ]
        );

        // Check if we have the correct mandate
        $this->assertMandate($mandate, $mandateMock);
    }
}
