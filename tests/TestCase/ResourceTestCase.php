<?php

namespace Mollie\API\Tests\TestCase;

use PHPUnit\Framework\TestCase;
use Mollie\API\Mollie;
use Mollie\API\Model;
use Mollie\API\Request;

class ResourceTestCase extends TestCase
{
    use ModelAssertions;

    /**
     * Get mocked customer
     * @return object Customer response object
     */
    protected function getCustomer()
    {
        return (object) [
            "resource" => "customer",
            "id" => "cst_test",
            "mode" => "test",
            "name" => "Customer",
            "email" => "customer@example.org",
            "locale" => "nl_NL",
            "metadata" => json_encode([
                'orderno' => 404
            ]),
            "recentlyUsedMethods" => (object) [
                "creditcard",
                "ideal"
            ],
            "createdDatetime" => "2016-04-06T13:23:21.0Z"
        ];
    }

    /**
     * Check customer object
     *
     * @param Mollie\API\Model\Customer $customer
     * @param object $reference Reference object (raw response)
     */
    protected function assertCustomer($customer, $reference)
    {
        $this->assertInstanceOf(Model\Customer::class, $customer);

        // Check customer details
        $this->assertModel($customer, $reference, [
            'id',
            'mode',
            'name',
            'email',
            'locale',
            'recentlyUsedMethods',
            'metadata',
            'createdDatetime'
        ]);
    }

    /**
     * Check multiple customer objects
     *
     * @param Mollie\API\Model\Customer[] $customers
     * @param object[] $references Reference object (raw response)
     */
    protected function assertCustomers(array $customers, array $references)
    {
        $this->assertModels($customers, $references, [$this, 'assertCustomer']);
    }

    /**
     * Get mocked customer mandate
     * @return object Customer mandate response object
     */
    protected function getMandate($status = 'valid')
    {
        return (object) [
            "resource" => "mandate",
            "id" => "mdt_test",
            "status" => $status,
            "method" => "creditcard",
            "customerId" => "cst_test",
            "details" => (object) [
                "cardHolder" => "John Doe",
                "cardNumber" => "1234",
                "cardLabel" => "Mastercard",
                "cardFingerprint" => "fHB3CCKx9REkz8fPplT8N4nq",
                "cardExpiryDate" => "2016-03-31"
            ],
            "createdDatetime" => "2016-04-13T11:32:38.0Z"
        ];
    }

    /**
     * Check customer mandate object
     *
     * @param Mollie\API\Model\Mandate $mandate
     * @param object $reference Reference object (raw response)
     */
    protected function assertMandate($mandate, $reference)
    {
        $this->assertInstanceOf(Model\Mandate::class, $mandate);

        // Check mandate details
        $this->assertModel($mandate, $reference, [
            'id',
            'status',
            'method',
            'customerId',
            'details',
            'createdDatetime'
        ]);
    }

    /**
     * Check multiple customer mandate objects
     *
     * @param Mollie\API\Model\Mandate[] $mandates
     * @param object[] $references Reference object (raw response)
     */
    protected function assertMandates(array $mandates, array $references)
    {
        $this->assertModels($mandates, $references, [$this, 'assertMandate']);
    }

    /**
     * Get mocked subscription
     * @return object Subscription response object
     */
    protected function getSubscription()
    {
        return (object) [
            "resource" => "subscription",
            "id" => "sub_test",
            "customerId" => "cst_test",
            "mode" => "test",
            "createdDatetime" => "2016-06-01T12:23:34.0Z",
            "status" => "active",
            "amount" => "25.00",
            "times" => 4,
            "interval" => "3 months",
            "description" => "Quarterly payment",
            "method" => null,
            "cancelledDatetime" => null,
            "links" => (object) [
                "webhookUrl" => "https://example.org/payments/webhook"
            ]
        ];
    }

    /**
     * Check customer subscription object
     *
     * @param Mollie\API\Model\Subscription $subscription
     * @param object $reference
     */
    protected function assertSubscription($subscription, $reference)
    {
        $this->assertInstanceOf(Model\Subscription::class, $subscription);

        // Check subscription details
        $this->assertModel($subscription, $reference, [
            'id',
            'mode',
            'status',
            'amount',
            'times',
            'interval',
            'description',
            'method',
            'customerId',
            'createdDatetime',
            'cancelledDatetime',
            'links'
        ]);
    }

    /**
     * Check multiple subscription objects
     *
     * @param Mollie\API\Model\Subscription[] $subscriptions
     * @param object[] $references Reference object (raw response)
     */
    protected function assertSubscriptions(array $subscriptions, array $references)
    {
        $this->assertModels($subscriptions, $references, [$this, 'assertSubscription']);
    }

    /**
     * Get mocked payment
     * @return object Payment response object
     */
    protected function getPayment()
    {
        return (object) [
            "id" => "tr_test",
            "mode" => "test",
            "createdDatetime" => "2016-08-01T10:57:45.0Z",
            "status" => "paid",
            "paidDatetime" => "2016-08-01T11:02:28.0Z",
            "amount" => 35.07,
            "description" => "Order 33",
            "method" => "ideal",
            "metadata" => json_encode([
                "order_id" => "33"
            ]),
            "details" => (object) [
                "consumerName" => "John Doe",
                "consumerAccount" => "NL53INGB0000000000",
                "consumerBic" => "INGBNL2A"
            ],
            "locale" => "nl",
            "profileId" => "pfl_test",
            "links" => (object) [
                "webhookUrl" => "https://webshop.example.org/payments/webhook",
                "redirectUrl" => "https://webshop.example.org/order/33/"
            ]
        ];
    }

    /**
     * Check payment object
     *
     * @param Mollie\API\Model\Payment $payment
     * @param object $reference
     */
    protected function assertPayment($payment, $reference)
    {
        $this->assertInstanceOf(Model\Payment::class, $payment);

        // Check payment details
        $this->assertModel($payment, $reference, [
            'id',
            'mode',
            'status',
            'description',
            'metadata',
            'locale',
            'links',
            'amount',
            'amountRefunded',
            'amountRemaining',
            'createdDatetime',
            'paidDatetime',
            'cancelledDatetime',
            'expiredDatetime',
            'expiryPeriod',
            'method',
            'details',
            'profileId',
            'settlementId'
        ]);
    }

    /**
     * Check multiple payment objects
     *
     * @param Mollie\API\Model\Payment[] $payments
     * @param object[] $references Reference object (raw response)
     */
    protected function assertPayments(array $payments, array $references)
    {
        $this->assertModels($payments, $references, [$this, 'assertPayment']);
    }

    /**
     * Get mocked issuer
     * @return object Issuer response object
     */
    protected function getIssuer()
    {
        return (object) [
            "resource" => "issuer",
            "id" => "ideal_ABNANL2A",
            "name" => "ABN AMRO",
            "method" => "ideal"
        ];
    }

    /**
     * Check issuer object
     *
     * @param Mollie\API\Model\Issuer $issuer
     * @param object $reference
     */
    protected function assertIssuer($issuer, $reference)
    {
        $this->assertInstanceOf(Model\Issuer::class, $issuer);

        // Check issuer details
        $this->assertModel($issuer, $reference, [
            'id',
            'name',
            'method'
        ]);
    }

    /**
     * Check multiple issuer objects
     *
     * @param Mollie\API\Model\Issuer[] $issuers
     * @param object[] $references Reference object (raw response)
     */
    protected function assertIssuers(array $issuers, array $references)
    {
        $this->assertModels($issuers, $references, [$this, 'assertIssuer']);
    }

    /**
     * Get mocked method
     * @return object Method response object
     */
    protected function getMethod()
    {
        return (object) [
            "id" => "ideal",
            "description" => "iDeal",
            "amount" => (object) [
                "minimum" => "0.31",
                "maximum" => "10000.00"
            ],
            "image" => (object) [
                "normal" => "https://www.mollie.com/images/payscreen/methods/ideal.png",
                "bigger" => "https://www.mollie.com/images/payscreen/methods/ideal@2x.png"
            ]
        ];
    }

    /**
     * Check method object
     *
     * @param Mollie\API\Model\Method $method
     * @param object $reference
     */
    protected function assertMethod($method, $reference)
    {
        $this->assertInstanceOf(Model\Method::class, $method);

        // Check method details
        $this->assertModel($method, $reference, [
            'id',
            'description',
            'amount',
            'image'
        ]);

        // Image
        $this->assertStringStartsWith('https://', $method->image->normal);
        $this->assertStringStartsWith('https://', $method->image->bigger);

        $this->assertStringStartsWith('<img', $method->image());
        $this->assertStringStartsWith('<img', $method->image('normal'));
        $this->assertStringStartsWith('<img', $method->image('bigger'));

        // Amount
        $this->assertEquals($reference->amount->minimum, $method->getMinimumAmount());
        $this->assertEquals($reference->amount->maximum, $method->getMaximumAmount());

        $this->assertNotNull($reference->amount->minimum, $method->amount->minimum);
        $this->assertNotNull($reference->amount->maximum, $method->amount->maximum);

        $this->assertEquals('double', gettype($method->amount->minimum));
        $this->assertEquals('double', gettype($method->amount->maximum));
        $this->assertEquals('double', gettype($method->getMinimumAmount()));
        $this->assertEquals('double', gettype($method->getMaximumAmount()));
    }

    /**
     * Check multiple method objects
     *
     * @param Mollie\API\Model\Method[] $methods
     * @param object[] $references Reference object (raw response)
     */
    protected function assertMethods(array $methods, array $references)
    {
        $this->assertModels($methods, $references, [$this, 'assertMethod']);
    }

    /**
     * Get mocked refund
     *
     * @param object $payment Payment response object
     * @return object Refund response object
     */
    protected function getRefund($payment = null)
    {
        $payment = isset($payment) ? $payment : $this->getPayment();

        return (object) [
            "id" => "re_test",
            "payment" => (object) $payment,
            "amount" => "5.95",
            "refundedDatetime" => "2016-08-07T15:43:16.0Z"
        ];
    }

    /**
     * Check refund object
     *
     * @param Mollie\API\Model\Refund $refund
     * @param object $reference
     */
    protected function assertRefund($refund, $reference)
    {
        $this->assertInstanceOf(Model\Refund::class, $refund);

        // Check refund details
        $this->assertModel($refund, $reference, [
            'id',
            'amount',
            'status',
            'refundedDatetime'
        ]);

        // Payment
        $this->assertInstanceOf(Model\Payment::class, $refund->payment());
        $this->assertPayment($refund->payment(), $reference->payment);
    }

    /**
     * Check multiple refund objects
     *
     * @param Mollie\API\Model\Refund[] $refunds
     * @param object[] $references Reference object (raw response)
     */
    protected function assertRefunds(array $refunds, array $references)
    {
        $this->assertModels($refunds, $references, [$this, 'assertRefund']);
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
