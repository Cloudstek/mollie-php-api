<?php

namespace Mollie\API\Tests\TestCase\Traits;

use Mollie\API\Mollie;
use Mollie\API\Model;

/**
 * Payment method assertions
 */
trait PaymentMethodAssertions
{
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
}
