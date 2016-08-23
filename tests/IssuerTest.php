<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Issuer;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class IssuerTest extends ResourceTestCase
{
    /**
     * Get issuer
     */
    public function testGetIssuer()
    {
        // Mock the issuer
        $issuerMock = $this->getIssuer();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo("/issuers/{$issuerMock->id}"))
            ->will($this->returnValue($issuerMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get issuer
        $issuer = $api->issuer($issuerMock->id)->get();
        $issuer2 = $api->issuer()->get($issuerMock->id);

        // Check issuer
        $this->assertEquals($issuer, $issuer2);
        $this->assertIssuer($issuer, $issuerMock);
    }

    /**
     * Get customer mandate through customer object
     *
     * Will first fetch the customer and then get the specified mandate.
     */
    public function testGetIssuerMethodFromModel()
    {
        // Mock the issuer
        $issuerMock = $this->getIssuer();

        // Mock the method
        $methodMock = $this->getMethod();

        // Mock the request
        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo("/methods/{$issuerMock->method}"))
            ->will($this->returnValue($methodMock));

        // Create API instance
        $api = new Mollie('test_testapikey');
        $api->request = $requestMock;

        // Get issuer
        $issuer = new Issuer($api, $issuerMock);

        // Get issuer method
        $method = $issuer->method();
        $this->assertMethod($method, $methodMock);
    }

    /**
     * Get all issuers
     */
    public function testGetIssuers()
    {
        // Prepare a list of issuers
        $issuerListMock = [];

        for ($i = 0; $i <= 15; $i++) {
            $issuer = $this->getIssuer();
            $issuerListMock[] = $issuer;
        }

        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, $issuerListMock, '/issuers');

        // Set request handler
        $api->request = $requestMock;

        // Get issuers
        $issuers = $api->issuer()->all();

        // Check the number of issuers returned
        $this->assertEquals(count($issuerListMock), count($issuers));

        // Check all issuers
        $this->assertIssuers($issuers, $issuerListMock);
    }

    /**
     * Get issuer without issuer ID
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage No issuer ID
     */
    public function testGetIssuerWithoutID()
    {
        $api = new Mollie('test_testapikey');
        $api->issuer()->get();
    }
}
