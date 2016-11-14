<?php

namespace Mollie\API\Tests\TestCase\Traits;

use Mollie\API\Mollie;
use Mollie\API\Model;

/**
 * Mandate assertions
 */
trait MandateAssertions
{
    /**
     * Get mocked customer mandate
     *
     * @param string $status Mandate status, valid or invalid
     * @param string $method Payment method ID, eg. creditcard
     * @param array  $details
     * @return object Customer mandate response object
     */
    protected function getMandate($status = 'valid', $method = 'creditcard', array $details = null)
    {
        if (empty($details)) {
            $details = [
                "cardHolder" => "John Doe",
                "cardNumber" => "1234",
                "cardLabel" => "Mastercard",
                "cardFingerprint" => "fHB3CCKx9REkz8fPplT8N4nq",
                "cardExpiryDate" => "2016-03-31"
            ];
        }

        return (object) [
            "resource" => "mandate",
            "id" => "mdt_test",
            "status" => $status,
            "method" => $method,
            "customerId" => "cst_test",
            "details" => (object) $details,
            "createdDatetime" => "2016-04-13T11:32:38.0Z"
        ];
    }

    /**
     * Get mocked SEPA customer mandate
     * @param  string $status [description]
     * @return [type] [description]
     */
    protected function getCustomerMandate($status = 'valid')
    {
        return $this->getMandate($status, 'directdebit', [
            'consumerName' => 'John doe',
            'consumerAccount' => 'NL53INGB0000000000',
            'consumerBic' => 'INGBNL2A',
            'signatureDate' => '2016-04-13T11:32:38+00:00',
            'mandateReference' => 'Evil Corp.'
        ]);
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
}
