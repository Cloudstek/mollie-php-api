<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Issuer;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class IssuerModelTest extends ResourceTestCase
{
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
}
