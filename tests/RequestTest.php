<?php

use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Exception\RequestException;
use Mollie\API\Tests\TestCase\ResourceTestCase;

class RequestTest extends ResourceTestCase
{
    /**
     * List request with multi-page response
     */
    public function testListRequest()
    {
        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMultiPageRequestMock($api, range(0, 19, 1), '/customers');

        // Do request
        $resp = $requestMock->getAll('/customers');

        // Check the number of items returned
        $this->assertEquals(20, count($resp));
        $this->assertEquals(range(0, 19, 1), $resp);
    }

    /**
     * List request with invalid response
     *
     * Do a list request and test response handling when navigation links element is missing from the response. This
     * should throw no exceptions and only a single page should be returned.
     */
    public function testInvalidListRequest()
    {
        // Create API instance
        $api = new Mollie('test_testapikey');

        // Mock the request handler
        $requestMock = $this->getMockBuilder(Request::class)
                            ->setConstructorArgs([$api])
                            ->setMethods(['get'])
                            ->getMock();

        // Build response data
        $data = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        // Mock the request get method
        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('/customers'))
            ->will($this->returnValue((object)[
                "totalCount" => count($data),
                "offset"    => 0,
                "count"     => count($data),
                "data"      => $data
            ]));

        // Do request
        $resp = $requestMock->get('/customers');

        // Check data
        $this->assertEquals($data, $resp->data);
    }

    /**
     * Test request exception
     */
    public function testRequestException()
    {
        // Exception constants
        $message = "Test exception";
        $code = 400;
        $url = "http://example.org/v1/test";
        $response = (object) [
            'body' => (object) [
                'error' => (object) ['message' => 'Test exception message from response']
            ]
        ];

        // Basic exception
        $exception = new RequestException($message);
        $this->assertContains($message, $exception->getMessage());

        // Exception with code
        $exception = new RequestException($message, $code);
        $this->assertContains((string)$code, $exception->getMessage());
        $this->assertContains($message, $exception->getMessage());

        // Exception with code and url
        $exception = new RequestException($message, $code, $url);
        $this->assertContains((string)$code, $exception->getMessage());
        $this->assertContains($url, $exception->getMessage());
        $this->assertContains($message, $exception->getMessage());

        // Exception with code, url and response
        $exception = new RequestException($message, $code, $url, $response);
        $this->assertContains((string)$code, $exception->getMessage());
        $this->assertContains($url, $exception->getMessage());
        $this->assertContains($message, $exception->getMessage());
        $this->assertContains($response->body->error->message, $exception->getMessage());
        $this->assertEquals($response, $exception->getResponse());
    }
}
