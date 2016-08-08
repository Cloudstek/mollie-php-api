<?php

use Mollie\API\Mollie;
use Mollie\API\Model;
use Mollie\API\Base\RequestBase;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class InitTest extends ResourceTestCase
{
    /**
     * Valid API key
     */
    public function testApiKey()
    {
        // Create API instance
        $api = new Mollie("test_testapikey");

        // Assert that API key is set correctly
        $this->assertEquals("test_testapikey", $api->getApiKey());
    }

    /**
     * Invalid API key should raise an exception
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid Mollie API key
     */
    public function testInvalidApiKey()
    {
        // Create API instance
        $api = new Mollie();

        // Set invalid API key and expect an exception
        $api->setApiKey('blabla_invalid_apikey');
    }

    /**
     * Valid API endpoint
     */
    public function testApiEndpoint()
    {
        // Create API instance
        $api = new Mollie(null, "http://testapi.test/api/v1/");

        // Assert that ending slash is trimmed
        $this->assertEquals("http://testapi.test/api/v1", $api->getApiEndpoint());

        // Assert that slashes are handled properly
        $this->assertEquals("http://testapi.test/api/v1/test", $api->getApiEndpoint('/test'));
        $this->assertEquals("http://testapi.test/api/v1/test", $api->getApiEndpoint('test'));
        $this->assertEquals("http://testapi.test/api/v1/test", $api->getApiEndpoint('/test/'));
        $this->assertEquals("http://testapi.test/api/v1/test", $api->getApiEndpoint('/test// '));

        // Set API endpoint
        $api->setApiEndpoint('http://testapi.test/api/v2/');

        // Assert that API endpoint is correctly set
        $this->assertEquals('http://testapi.test/api/v2', $api->getApiEndpoint());
    }

    /**
     * Invalid API endpoint should raise an exception
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid Mollie API endpoint
     */
    public function testInvalidApiEndpoint()
    {
        // Create API instance
        $api = new Mollie('test_testapikey');

        // Set invalid API endpoint and expect an exception
        $api->setApiEndpoint('ftp://invalidurl/api/v1');
    }

    /**
     * Test locale
     */
    public function testLocale()
    {
        // Create API instance
        $api = new Mollie('test_testapikey');
        $this->assertEquals(null, $api->getLocale());

        $api->setLocale('nl');
        $this->assertEquals('nl', $api->getLocale());
    }

    /**
     * Request handler
     */
    public function testRequestHandler()
    {
        // Create request class stub
        $requestHandler = $this->createMock(RequestBase::class);

        // Set request handler
        $api = new Mollie(null, null, $requestHandler);
        $this->assertEquals($requestHandler, $api->request);

        // Set request handler
        $api->request = $requestHandler;
        $this->assertEquals($requestHandler, $api->request);
    }

    /**
     * Invalid request handler
     *
     * Request handler should be of type RequestBase and should throw PHP error otherwise
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInvalidRequestHandler()
    {
        // Create invalid request handler
        $requestHandler = [];

        // Set request handler
        $api = new Mollie(null, null, $requestHandler);
    }

    /**
     * Do API request without API key
     *
     * @expectedException Mollie\API\Exception\RequestException
     * @expectedExcetionMessage No API key
     */
    public function testRequestWithoutApiKey()
    {
        // Create API instance
        $api = new Mollie();

        // Do request without API key set
        $api->request->get('http://exception.oops');
    }
}
