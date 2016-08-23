<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class ResourceTest extends ResourceTestCase
{
    /**
     * Get resource by model
     */
    public function testGetResourceByModel()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("/customers/{$customerMock->id}"))
            ->will($this->returnValue($customerMock));

        // Initialize API
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get the customer
        $customerModel = new Model\Customer($api, $customerMock);

        // Get the customer
        $customer = $api->customer($customerModel)->get();

        // Assert that the customer ID's are equal
        $this->assertEquals($customer, $customerModel);
        $this->assertCustomer($customer, $customerMock);
    }

    /**
     * Get resource by using an invalid model
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Customer argument must
     */
    public function testGetResourceByInvalidModel()
    {
        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->never())
            ->method('get')
            ->with($this->equalTo("/customers/mdt_test"));

        // Initialize API
        $api = new Mollie('test_testapikey', null, $requestMock);

        // Mock the mandate
        $mandate = new Model\Mandate($api, ['id' => 'mdt_test']);

        // Get the customer with a mandate object (invalid)
        $customer = $api->customer($mandate)->get();
    }

    /**
     * Get raw response from model
     */
    public function testModelRawResponse()
    {
        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the customer
        $customerMock = $this->getCustomer();

        // Get the customer
        $customer = new Model\Customer($api, $customerMock);

        $this->assertTrue(method_exists($customer, 'getResponse'));
        $this->assertEquals($customerMock, $customer->getResponse());
    }

    /**
     * Test model raw response type
     *
     * Fill model with array instead of object to test array->object conversion to normalize response object type.
     */
    public function testModelRawResponseType()
    {
        // Mock the customer as array
        $customerMock = $this->getCustomer();
        $customerArrayMock = json_decode(json_encode($customerMock), true);

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get the customer
        $customer = new Model\Customer($api, $customerArrayMock);

        $this->assertTrue(method_exists($customer, 'getResponse'));
        $this->assertTrue(is_object($customer->getResponse()));
        $this->assertEquals($customerMock, $customer->getResponse());
    }

    /**
     * Fill model with invalid data
     *
     * @expectedException InvalidArgumentException
     */
    public function testFillModelInvalidData()
    {
        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get the customer
        $customer = new Model\Customer($api, false);
    }

    /**
     * Fill model with data that contains invalid date
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Property createdDatetime is not a valid ISO 8601
     */
    public function testFillModelInvalidDate()
    {
        // Mock the customer
        $customerMock = $this->getCustomer();
        $customerMock->createdDatetime = 'asd';

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get customer
        $customer = new Model\Customer($api, $customerMock);
    }

    /**
     * Fill model with data that contains invalid duration
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Property expiryPeriod is not a valid ISO 8601
     */
    public function testFillModelInvalidDuration()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();
        $paymentMock->expiryPeriod = 'asd';

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get payment
        $payment = new Model\Payment($api, $paymentMock);
    }

    /**
     * Fill model with data that contains invalid JSON metadata
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Property metadata does not contain
     */
    public function testFillModelInvalidMetadata()
    {
        // Mock the payment
        $paymentMock = $this->getPayment();
        $paymentMock->metadata = '{asdaSD}';

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Get payment
        $payment = new Model\Payment($api, $paymentMock);
    }
}
