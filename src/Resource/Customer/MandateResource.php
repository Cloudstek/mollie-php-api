<?php

namespace Mollie\API\Resource\Customer;

use Mollie\API\Mollie;
use Mollie\API\Resource\Base\CustomerResourceBase;
use Mollie\API\Model\Customer;
use Mollie\API\Model\Mandate;

class MandateResource extends CustomerResourceBase
{
    /** @var string $mandate */
    protected $mandate;

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

        if(isset($mandate)) {
            $this->mandate = $this->_getMandateID($mandate);
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
        $mandate_id = $this->_getMandateID($id);

        // Get mandate
        $resp = $this->api->request->get("/customer/{$this->customer}/mandates/{$mandate_id}");

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
        $resp = $this->api->request->getAll("/customer/{$this->customer}/mandates");

        if(!empty($resp) && is_array($resp)) {
            foreach ($resp as $item) {
                $items[] = new Mandate($this->api, $item);
            }
        }

        return $items;
    }

    /**
     * Get all customer mandates as generator
     * @return Generator
     */
    public function yieldAll()
    {
        // Get all mandates
        $items = $this->api->request->getAll("/customer/{$this->customer}/mandates");

        // Yield all mandates
        foreach ($items as $item) {
            yield new Mandate($this->api, $item);
        }
    }
}
