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
     * @param string $mandateId Mandate ID
     * @return Mandate
     */
    public function get($mandateId = null)
    {
        // Get mandate ID
        $mandateId = $this->getMandateID($mandateId);

        // Get mandate
        $resp = $this->api->request->get("/customers/{$this->customer}/mandates/{$mandateId}");

        // Return mandate model
        return new Mandate($this->api, $resp);
    }

    /**
     * Get all customer mandates
     * @return Mandate[]
     */
    public function all()
    {
        $items = array();

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
     * @see https://www.mollie.com/en/docs/reference/mandates/create
     * @param string $name Consumer name
     * @param string $account Consumer IBAN account number
     * @param array  $opts
     *                  [consumerBic]       string      The consumer's bank's BIC / SWIFT code.
     *                  [signatureDate]     DateTime    Signature date
     *                  [mandateReference]  string      Custom reference
     * @return Mandate
     */
    public function create($name, $account, array $opts = array())
    {
        // Signature date
        if (!empty($opts['signatureDate'])) {
            if (!($opts['signatureDate'] instanceof \DateTime)) {
                throw new \InvalidArgumentException("Argument signatureDate must be of type DateTime.");
            }

            $opts['signatureDate'] = $opts['signatureDate']->format('c');
        }

        // Construct parameters
        $params = array(
            'method' => 'directdebit',
            'consumerName' => $name,
            'consumerAccount' => $account
        );

        // Merge options
        $params = array_merge($params, $opts);

        // Create mandate
        $resp = $this->api->request->post("/customers/{$this->customer}/mandates", $params);

        // Return mandate model
        return new Mandate($this->api, $resp);
    }

    /**
     * Revoke customer mandate
     *
     * @param string|null $mandateId Mandate ID
     */
    public function revoke($mandateId = null)
    {
        // Get mandate ID
        $mandateId = $this->getMandateID($mandateId);

        // Revoke mandate
        $this->api->request->delete("/customers/{$this->customer}/mandates/{$mandateId}");
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
