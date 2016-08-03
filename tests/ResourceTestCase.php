<?php

namespace Mollie\API\Tests;

use PHPUnit\Framework\TestCase;
use Mollie\API\Mollie;
use Mollie\API\Request;

class ResourceTestCase extends TestCase
{
    /**
     * Multi-page request mock
     *
     * @param  Mollie $api API instance
     * @param  array $data
     * @param  string $endpoint [description]
     * @return  [description]
     */
    protected function getMultiPageRequestMock(Mollie $api, $data, $endpoint)
    {
        // Epic math skills
        $totalCount = count($data);
        $dataPageOne = array_slice($data, 0, floor($totalCount / 2));
        $dataPageTwo = array_slice($data, floor($totalCount / 2));

        $nextLink = $api->getApiEndpoint($endpoint, ['offset' => count($dataPageOne)]);

        // Mock the request handler
        $requestMock = $this->getMockBuilder(Request::class)
                            ->setConstructorArgs([$api])
                            ->setMethods(['get'])
                            ->getMock();

        // Mock the request get method
        $requestMock
            ->expects($this->atLeastOnce())
            ->method('get')
            ->withConsecutive(
                $this->equalTo($endpoint),
                $this->equalTo($nextLink)
            )
            ->will($this->returnCallback(
                function($url) use($totalCount, $dataPageOne, $dataPageTwo, $nextLink)
                {
                    if($url == $nextLink) {
                        return (object)[
                            "totalCount"    => $totalCount,
                            "offset"        => count($dataPageOne),
                            "count"         => count($dataPageTwo),
                            "data"          => $dataPageTwo,
                            "links"         => (object) [
                                "next"      => null
                            ]
                        ];
                    }

                    return (object)[
                        "totalCount"    => $totalCount,
                        "offset"        => 0,
                        "count"         => count($dataPageOne),
                        "data"          => $dataPageOne,
                        "links"         => (object) [
                            "next"      => $nextLink
                        ]
                    ];
                }
            ));
            
        return $requestMock;
    }
}
