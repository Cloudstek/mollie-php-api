<?php

namespace Mollie\API\Resource;

use Mollie\API\Model\Issuer;
use Mollie\API\Resource\Base\ResourceBase;

class IssuerResource extends ResourceBase
{
    /**
     * Get issuer
     *
     * @param Issuer|string $id Issuer ID
     * @return Issuer
     */
    public function get($id = null)
    {
        // Get issuer ID
        $id = $this->_getResourceID($id, Issuer::class);

        // Get issuer
        $resp = $this->api->request->get("/issuers/{$id}");

        // Return issuer model
        return new Issuer($this->api, $resp);
    }

    /**
     * Get all issuers
     * @return Issuer[]
     */
    public function all()
    {
        $items = [];

        // Get all issuers
        $resp = $this->api->request->getAll("/issuers");

        if (!empty($resp) && is_array($resp)) {
            foreach ($resp as $item) {
                $items[] = new Issuer($this->api, $item);
            }
        }

        return $items;
    }
}
