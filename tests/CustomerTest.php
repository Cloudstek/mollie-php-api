<?php

use Mollie\API\Tests\ResourceTestCase;
use Mollie\API\Mollie;
use Mollie\API\Request;

class CustomerTest extends ResourceTestCase
{
    /** @var object $johnDoe Customer */
    protected $johnDoe;

    /**
     * Set Up
     */
    public function setUp()
    {
        // Create or John Doe customer
        $this->johnDoe = (object) [
            "resource" => "customer",
            "id" => "cst_test",
            "mode" => "test",
            "name" => "Customer",
            "email" => "customer@example.org",
            "locale" => "nl_NL",
            "metadata" => (object) [
                'orderno' => 404
            ],
            "recentlyUsedMethods" => (object) [
                "creditcard",
                "ideal"
            ],
            "createdDatetime" => "2016-04-06T13:23:21.0Z"
        ];
    }

    /**
     * Get customer
     */
    public function testGetCustomer()
    {
        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("/customers/cst_test"))
            ->will($this->returnValue($this->johnDoe));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get customer
        $customer = $api->customer('cst_test')->get();

        // Check if we have the correct customer
        $this->assertEquals($this->johnDoe->id, $customer->id);
        $this->assertEquals($this->johnDoe->mode, $customer->mode);

        // Check if JSON metadata is correctly parsed
        $this->assertEquals($this->johnDoe->metadata, $customer->metadata);

        // Check if date objects are parsed correctly
        $this->assertEquals(strtotime($this->johnDoe->createdDatetime), $customer->createdDatetime->format('U'));
    }

    /**
     * Get all customers
     */
    public function testGetCustomers()
    {
        // Prepare a list of John doe customers
        $johnDoeList = [];

        for($i = 0; $i <= 15; $i++) {
            $john = clone $this->johnDoe;

            $john->id .= "_{$i}";   // cst_test_1
            $john->name .= " {$i}"; // Customer 1

            $johnDoeList[] = $john;
        }

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $johnDoeList, '/customers');

        // Set request handler
        $api->request = $requestMock;

        // Get customers
        $customers = $api->customer()->all();

        // Check the number of customers returned
        $this->assertEquals(count($johnDoeList), count($customers));

        // Get mandates through generator
        $customers = [];

        foreach($api->customer()->yieldAll() as $customer) {
            $customers[] = $customer;
        }

        // Check the number of customers returned
        $this->assertEquals(count($johnDoeList), count($customers));
    }

    /**
     * Create customer
     */
    public function testCreateCustomer()
    {
        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("/customers"),
                $this->equalTo([
                    'name'      => $this->johnDoe->name,
                    'email'     => $this->johnDoe->email,
                    'locale'    => $this->johnDoe->locale,
                    'metadata'  => json_encode($this->johnDoe->metadata)
                ])
            )
            ->will($this->returnValue($this->johnDoe));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Create customer
        $customer = $api->customer()->create($this->johnDoe->name, $this->johnDoe->email, $this->johnDoe->locale, (array) $this->johnDoe->metadata);

        // Check if we have the correct customer
        $this->assertEquals($this->johnDoe->id, $customer->id);
        $this->assertEquals($this->johnDoe->mode, $customer->mode);

        // Check if JSON metadata is correctly parsed
        $this->assertEquals($this->johnDoe->metadata, $customer->metadata);

        // Check if date objects are parsed correctly
        $this->assertEquals(strtotime($this->johnDoe->createdDatetime), $customer->createdDatetime->format('U'));
    }
}
