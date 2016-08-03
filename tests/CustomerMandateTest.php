<?php

use Mollie\API\Tests\ResourceTestCase;
use Mollie\API\Mollie;
use Mollie\API\Request;

class CustomerMandateTest extends ResourceTestCase
{
    /** @var object $johnDoeMandate Mandate for John Doe */
    protected $johnDoeMandate;

    /**
     * Set Up
     */
    public function setUp()
    {
        // Create valid mandate for John Doe
        $this->johnDoeMandate = (object) [
            "resource" => "mandate",
            "id" => "mdt_test",
            "status" => "valid",
            "method" => "creditcard",
            "customerId" => "cst_test",
            "details" => (object) [
                "cardHolder" => "John Doe",
                "cardNumber" => "1234",
                "cardLabel" => "Mastercard",
                "cardFingerprint" => "fHB3CCKx9REkz8fPplT8N4nq",
                "cardExpiryDate" => "2016-03-31"
            ],
            "createdDatetime" => "2016-04-13T11:32:38.0Z"
        ];
    }

    /**
     * Get customer mandate
     */
    public function testGetCustomerMandate()
    {
        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("/customer/cst_test/mandates/mdt_test"))
            ->will($this->returnValue($this->johnDoeMandate));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get customer mandate
        $mandate = $api->customer('cst_test')->mandate('mdt_test')->get();

        // Check if we have the correct mandate
        $this->assertEquals($this->johnDoeMandate->id, $mandate->id);
        $this->assertEquals($this->johnDoeMandate->status, $mandate->status);

        // Check if date objects are parsed correctly
        $this->assertEquals(strtotime($this->johnDoeMandate->createdDatetime), $mandate->createdDatetime->format('U'));
    }

    /**
     * Get all customer mandates
     */
    public function testGetCustomerMandates()
    {
        // Prepare a list of mandates for John Doe
        $johnDoeMandateList = [];

        for($i = 0; $i <= 15; $i++) {
            $mandate = clone $this->johnDoeMandate;
            $mandate->id .= "_{$i}";   // mdt_test_1

            // Leave one valid mandate
            if($i < 15) {
                $mandate->status = "invalid";
            }

            // Add mandate to list
            $johnDoeMandateList[] = $mandate;
        }

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $johnDoeMandateList, '/customer/cst_test/mandates');

        // Set request handler
        $api->request = $requestMock;

        // Get mandates
        $mandates = $api->customer('cst_test')->mandate()->all();

        // Check the number of mandates returned
        $this->assertEquals(count($johnDoeMandateList), count($mandates));

        // Get mandates through generator
        $mandates = [];

        foreach($api->customer('cst_test')->mandate()->yieldAll() as $mandate) {
            $mandates[] = $mandate;
        }

        // Check the number of mandates returned
        $this->assertEquals(count($johnDoeMandateList), count($mandates));
    }
}
