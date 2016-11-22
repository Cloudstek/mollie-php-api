<?php

namespace Mollie\API\Resource;

use Mollie\API\Model\Refund;
use Mollie\API\Resource\Base\ResourceBase;

class RefundResource extends ResourceBase
{
    /**
     * Get all refunds
     * @return Generator|Refund[]
     */
    public function all()
    {
        $items = array();

        // API request
        $resp = $this->api->request->getAll("/refunds");

        foreach ($resp as $item) {
            $items[] = new Refund($this->api, $item);
        }

        return $items;
    }
}
