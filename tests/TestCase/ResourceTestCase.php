<?php

namespace Mollie\API\Tests\TestCase;

use PHPUnit\Framework\TestCase;
use Mollie\API\Mollie;
use Mollie\API\Request;
use Mollie\API\Model\Base\ModelBase;

abstract class ResourceTestCase extends TestCase
{
    use Traits\CustomerAssertions;
    use Traits\IssuerAssertions;
    use Traits\MandateAssertions;
    use Traits\PaymentAssertions;
    use Traits\PaymentMethodAssertions;
    use Traits\PaymentRefundAssertions;
    use Traits\SubscriptionAssertions;

    /**
     * Check model against response object
     *
     * @param ModelBase $model Resource model to validate
     * @param array|object $reference Reference object (raw response)
     * @param array $mapping Field mapping between resource and reference object
     */
    protected function assertModel(ModelBase $model, $reference, array $mapping)
    {
        foreach ($mapping as $k => $v) {
            $k = is_int($k) ? $v : $k; // Handle non-associative arrays

            if (is_array($reference)) {
                $reference = (object) $reference;
            }

            if (!property_exists($reference, $k)) {
                continue;
            }

            // ISO 8601 Date
            if (preg_match('/.+(Datetime|Date)$/', $k) && isset($reference->$k)) {
                $this->assertInstanceOf(\DateTime::class, $model->$v);
                $this->assertEquals(strtotime($reference->$k), $model->$v->format('U'));
                continue;
            }

            // ISO 8601 Duration
            if (preg_match('/.+(Period)$/', $k) && preg_match('/^P.+/', $reference->$k)) {
                $this->assertInstanceOf(\DateInterval::class, $model->$v);
                continue;
            }

            // Amount
            if (preg_match('/amount.*$/', $k) && !is_object($reference->$k)) {
                $this->assertTrue(is_float($model->$v) || is_integer($model->$v));
                $this->assertEquals($reference->$k, $model->$v);
                continue;
            }

            $this->assertEquals($reference->$k, $model->$v);
        }
    }

    /**
     * Check multiple models against response objects
     *
     * @param Mollie\API\Model\Base\ModelBase[] $models Resource models to validate
     * @param object[] $references Reference objects (raw responses)
     * @param callable $callback Method to use for validating models
     */
    protected function assertModels(array $models, array $references, callable $callback)
    {
        $numModels = count($models);

        // Check number of references
        if ($numModels !== count($references)) {
            throw new \InvalidArgumentException("Number of models to test must equal number of references.");
        }

        // Test all models
        for ($i = 0; $i < $numModels; $i++) {
            call_user_func_array($callback, array($models[$i], $references[$i]));
        }
    }

    /**
     * Check model methods
     *
     * @param ModelBase $model Resource model to validate
     * @param array $methods Methods that must be available and callable
     */
    protected function assertModelMethods(ModelBase $model, array $methods)
    {
        foreach ($methods as $method) {
            $this->assertTrue(method_exists($model, $method));
            $this->assertTrue(is_callable([$model, $method]));
        }
    }

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
                function ($url) use ($totalCount, $dataPageOne, $dataPageTwo, $nextLink) {
                    if ($url == $nextLink) {
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
