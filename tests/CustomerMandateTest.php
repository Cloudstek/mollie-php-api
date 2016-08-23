<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Mandate;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class CustomerMandateTest extends ResourceTestCase
{
    /**
     * Get customer mandate
     */
    public function testGetCustomerMandate()
    {
        // Mock the mandate
        $mandateMock = $this->getMandate();

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo("/customers/{$customerMock->id}/mandates/{$mandateMock->id}"))
            ->will($this->returnValue($mandateMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get customer mandate
        $mandate = $api->customer($customerMock->id)->mandate($mandateMock->id)->get();
        $mandate2 = $api->customer($customerMock->id)->mandate()->get($mandateMock->id);

        // Check if we have the correct mandate
        $this->assertEquals($mandate, $mandate2);
        $this->assertMandate($mandate, $mandateMock);
    }

    /**
     * Get all customer mandates
     */
    public function testGetCustomerMandates()
    {
        // Prepare a list of mandates
        $mandateListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $mandate = $this->getMandate($i < 15 ? "invalid" : "valid");
            $mandate->id .= "_{$i}";   // mdt_test_1

            // Add mandate to list
            $mandateListMock[] = $mandate;
        }

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $mandateListMock, "/customer/{$customerMock->id}/mandates");

        // Set request handler
        $api->request = $requestMock;

        // Get mandates
        $mandates = $api->customer($customerMock->id)->mandate()->all();

        // Check the number of mandates returned
        $this->assertEquals(count($mandateListMock), count($mandates));

        // Check all mandates
        $this->assertMandates($mandates, $mandateListMock);
    }

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
                'bic' => $mandateMock->details->consumerBic,
                'signatureDate' => new \DateTime($mandateMock->details->signatureDate),
                'reference' => $mandateMock->details->mandateReference
            ]
        );

        // Check if we have the correct mandate
        $this->assertMandate($mandate, $mandateMock);
    }

    /**
     * Get customer for mandate object
     */
    public function testGetCustomerForMandate()
    {
        // Mock the mandate
        $mandateMock = $this->getMandate();

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("/customers/{$customerMock->id}"))
            ->will($this->returnValue($customerMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get customer mandate
        $mandate = new Mandate($api, $mandateMock);

        // Get customer
        $customer = $mandate->customer();
        $this->assertCustomer($customer, $customerMock);
    }

    /**
     * Get mandate status
     */
    public function testCustomerMandateStatus()
    {
        // Mock the mandate
        $mandateMock = $this->getMandate();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get mandate
        $mandate = new Mandate($api, $mandateMock);

        // Check valid status
        $mandate->status = "valid";
        $this->assertFalse($mandate->isInvalid());
        $this->assertTrue($mandate->isValid());

        // Check invalid status
        $mandate->status = "invalid";
        $this->assertTrue($mandate->isInvalid());
        $this->assertFalse($mandate->isValid());
    }

    /**
     * Check if a list of mandates has a valid mandate
     */
    public function testCustomerMandateHasValid()
    {
        // Prepare a list of mandates
        $mandateListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $mandate = $this->getMandate("invalid");
            $mandate->id .= "_{$i}";   // mdt_test_1

            // Add mandate to list
            $mandateListMock[] = $mandate;
        }

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $mandateListMock, "/customer/{$customerMock->id}/mandates");

        // Set request handler
        $api->request = $requestMock;

        // Mandate resource
        $customerMandate = $api->customer($customerMock->id)->mandate();

        // Make sure no valid mandates are found
        $this->assertFalse($customerMandate->hasValid());

        // Set a mandate to valid and check again
        $mandateListMock[10]->status = 'valid';
        $this->assertTrue($customerMandate->hasValid());
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

    /**
     * Get customer mandate without mandate ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No mandate ID
     */
    public function testGetCustomerMandateWithoutID()
    {
        $api = new Mollie('test_testapikey');
        $api->customer('cst_test')->mandate()->get();
    }

    /**
     * Get customer mandate without customer ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No customer ID
     */
    public function testGetCustomerMandateWithoutCustomerID()
    {
        $api = new Mollie('test_testapikey');
        $api->customer()->mandate('mdt_test')->get();
    }

    /**
     * Get customer mandate without any arguments (doh!)
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No customer ID
     */
    public function testGetCustomerMandateWithoutAnything()
    {
        $api = new Mollie('test_testapikey');
        $api->customer()->mandate()->get();
    }
}
