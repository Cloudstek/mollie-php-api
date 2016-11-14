<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Customer creation tests
 */
class CustomerCreateTest extends ResourceTestCase
{
    /**
     * Create customer
     */
    public function testCreateCustomer()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("/customers"),
                $this->equalTo([
                    'name'      => $customerMock->name,
                    'email'     => $customerMock->email,
                    'locale'    => null,
                    'metadata'  => $customerMock->metadata
                ])
            )
            ->will($this->returnValue($customerMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Create customer
        $customer = $api->customer()->create($customerMock->name, $customerMock->email, $customerMock->metadata);

        // Check if we have the correct customer
        $this->assertCustomer($customer, $customerMock);
    }

    /**
     * Create customer with invalid metadata
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Metadata argument must be of type
     */
    public function testCreateCustomerWithInvalidMetadata()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post')
            ->with($this->equalTo("/customers"));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Create customer
        $api->customer()->create($customerMock->name, $customerMock->email, 'my fancy falsy metadata');
    }
}
