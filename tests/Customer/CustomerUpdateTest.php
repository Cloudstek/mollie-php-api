<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

/**
 * Customer update tests
 */
class CustomerUpdateTest extends ResourceTestCase
{
    /**
     * Update customer details
     */
    public function testUpdateCustomer()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Clone the customer to update its details
        $updatedCustomerMock = clone $customerMock;
        $updatedCustomerMock->name = 'Updated' . $customerMock->name;
        $updatedCustomerMock->metadata = ['updated' => true];

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("/customers/{$customerMock->id}"),
                $this->equalTo([
                    'name'      => $updatedCustomerMock->name,
                    'metadata'  => $updatedCustomerMock->metadata
                ])
            )
            ->will($this->returnValue($updatedCustomerMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Update customer
        $customer = $api->customer($customerMock->id)->update($updatedCustomerMock->name, null, $updatedCustomerMock->metadata);

        // Check if we have the correct customer
        $this->assertCustomer($customer, $updatedCustomerMock);
    }

    /**
     * Update customer details without supplying parameters
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No arguments supplied
     */
    public function testUpdateCustomerWithoutParameters()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post')
            ->with($this->equalTo("/customers/{$customerMock->id}"));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Update customer
        $api->customer($customerMock->id)->update();
    }

    /**
     * Update customer details by supplying only empty parameters
     *
     * @expectedException InvalidArgumentException
     */
    public function testUpdateCustomerWithEmptyParameters()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post')
            ->with($this->equalTo("/customers/{$customerMock->id}"));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Update customer
        $api->customer($customerMock->id)->update('', '');
    }

    /**
     * Update customer details by unsetting the name with an empty string. Name is a required parameter.
     *
     * The update method requires at least 1 non-empty parameter to update the customer details.
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Name argument can't be an empty string
     */
    public function testUpdateCustomerWithEmptyName()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post')
            ->with($this->equalTo("/customers/{$customerMock->id}"));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Update customer
        $api->customer($customerMock->id)->update('', 'test@test.com');
    }

    /**
     * Update customer details by unsetting the email with an empty string. Email is a required parameter.
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Email argument can't be an empty string
     */
    public function testUpdateCustomerWithEmptyEmail()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post')
            ->with($this->equalTo("/customers/{$customerMock->id}"));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Update customer
        $api->customer($customerMock->id)->update('Test Customer', '');
    }

    /**
     * Update customer details with invalid metadata
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Metadata argument must be of typ
     */
    public function testUpdateCustomerWithInvalidMetadata()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('post')
            ->with($this->equalTo("/customers/{$customerMock->id}"));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Update customer
        $api->customer($customerMock->id)->update(null, null, 'my fancy invalid metadata');
    }
}
