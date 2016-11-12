<?php

namespace Mollie\API\Tests\TestCase\Traits;

use Mollie\API\Mollie;
use Mollie\API\Model;

/**
 * Issuer assertions
 */
trait IssuerAssertions
{
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
}
