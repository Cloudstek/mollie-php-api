<?php

namespace Mollie\API\Resource;

use Mollie\API\Mollie;
use Mollie\API\Mollie\Model\Issuer;

class IssuerResource extends Base\ResourceBase
{
    /**
     * Get iDEAL issuer
     *
     * @param string $id iDEAL issuer ID
     * @return Issuer
     */
    public function get($id = null)
    {
        // Get issuer ID
        $id = $this->_getResourceID($id);

        $resp = $this->api->request->get("/issuers/{$id}");

        // Return method model
        return new Issuer($this->api, $resp);
    }

    /**
     * Get all iDEAL issuers
     * @return Generator|Issuer[]
     */
    public function all()
    {
        $items = $this->api->request->getAll("/issuers");

        foreach ($items as $item) {
            yield new Issuer($this->api, $item);
        }
    }
}
