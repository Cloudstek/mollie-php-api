<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class PaymentMethodTest extends ResourceTestCase
{
    /**
     * Get method
     */
    public function testGetPaymentMethod()
    {
        // Mock the method
        $methodMock = $this->getMethod();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo("/methods/{$methodMock->id}"))
            ->will($this->returnValue($methodMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get method
        $method = $api->method($methodMock->id)->get();
        $method2 = $api->method()->get($methodMock->id);

        // Check method
        $this->assertEquals($method, $method2);
        $this->assertMethod($method, $methodMock);
    }

    /**
     * Get all methods
     */
    public function testGetPaymentMethods()
    {
        // Prepare a list of methods
        $methodListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $method = $this->getMethod();
            $methodListMock[] = $method;
        }

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $methodListMock, '/methods');

        // Set request handler
        $api->request = $requestMock;

        // Get methods
        $methods = $api->method()->all();

        // Check the number of methods returned
        $this->assertEquals(count($methodListMock), count($methods));
    }

    /**
     * Get method image with invalid size
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Image size 'superduper' for payment method
     */
    public function testGetMethodImageWithInvalidSize()
    {
        // Mock the method
        $methodMock = $this->getMethod();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("/methods/{$methodMock->id}"))
            ->will($this->returnValue($methodMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get method
        $method = $api->method($methodMock->id)->get();

        // Check method
        $this->assertMethod($method, $methodMock);

        // Get image
        $method->image('superduper');
    }

    /**
     * Get method without method ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No method ID
     */
    public function testGetMethodWithoutID()
    {
        $api = new Mollie('test_testapikey');
        $api->method()->get();
    }
}
