<?php

namespace Mollie\API\Tests\TestCase\Traits;

use Mollie\API\Mollie;
use Mollie\API\Model;

/**
 * Customer assertions
 */
trait CustomerAssertions
{
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
            "metadata" => (object) [
                'orderno' => 404
            ],
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
}
