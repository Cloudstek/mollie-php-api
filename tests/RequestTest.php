<?php

use Mollie\API\Tests\ResourceTestCase;
use Mollie\API\Mollie;
use Mollie\API\Request;

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
        $api = new Mollie();

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
}
