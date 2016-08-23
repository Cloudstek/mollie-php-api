<?php

namespace Mollie\API\Resource\Customer;

use Mollie\API\Mollie;
use Mollie\API\Resource\Base\CustomerResourceBase;
use Mollie\API\Model\Mandate;

class MandateResource extends CustomerResourceBase
{
    /**
     * Mandate resource constructor
     *
     * @param Mollie $api API reference
     * @param Customer|string $customer
     * @param Mandate|string $mandate
     */
    public function __construct(Mollie $api, $customer, $mandate = null)
    {
        parent::__construct($api, $customer);

        if (isset($mandate)) {
            $this->mandate = $this->getMandateID($mandate);
        }
    }

    /**
     * Get customer mandate
     *
     * @param string $id Mandate ID
     * @return Mandate
     */
    public function get($id = null)
    {
        // Get mandate ID
        $mandate_id = $this->getMandateID($id);

        // Get mandate
        $resp = $this->api->request->get("/customers/{$this->customer}/mandates/{$mandate_id}");

        // Return mandate model
        return new Mandate($this->api, $resp);
    }

    /**
     * Get all customer mandates
     * @return Mandate[]
     */
    public function all()
    {
        $items = [];

        // Get all customer mandates
        $resp = $this->api->request->getAll("/customers/{$this->customer}/mandates");

        if (!empty($resp) && is_array($resp)) {
            foreach ($resp as $item) {
                $items[] = new Mandate($this->api, $item);
            }
        }

        return $items;
    }

    /**
     * Create SEPA direct debit mandate
     *
     * @param string $name Consumer name
     * @param string $account Consumer IBAN account number
     * @param array  $opts
     *                  [bic]           string      Consumer BIC
     *                  [signatureDate] DateTime    Signature date
     *                  [reference]     string      Custom reference
     * @return Mandate
     */
    public function create($name, $account, array $opts = [])
    {
        if (!empty($opts['signatureDate'])) {
            if (!($opts['signatureDate'] instanceof \DateTime)) {
                throw new \InvalidArgumentException("Argument signatureDate must be of type DateTime.");
            }

            $opts['signatureDate'] = $opts['signatureDate']->format('c');
        }

        // Construct parameters
        $params = [
            'method' => 'directdebit',
            'consumerName' => $name,
            'consumerAccount' => $account,
            'consumerBic' => !empty($opts['bic']) ? $opts['bic'] : null,
            'signatureDate' => !empty($opts['signatureDate']) ? $opts['signatureDate'] : null,
            'mandateReference' => !empty($opts['reference']) ? $opts['reference'] : null
        ];

        // Create mandate
        $resp = $this->api->request->post("/customers/{$this->customer}/mandates", $params);

        // Return mandate model
        return new Mandate($this->api, $resp);
    }

    /**
     * Check if customer has any valid mandates
     * @return boolean
     */
    public function hasValid()
    {
        $mandates = $this->all();

        foreach ($mandates as $mandate) {
            if ($mandate->isValid()) {
                return true;
            }
        }

        return false;
    }
}
