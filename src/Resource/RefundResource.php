<?php

namespace Mollie\API\Resource;

use Mollie\API\Mollie;
use Mollie\API\Model\Refund;

class RefundResource extends Base\ResourceBase
{
    /**
     * Get all refunds
     * @return Generator|Refund[]
     */
    public function all()
    {
        $items = [];

        // API request
        $resp = $this->api->request->getAll("/refunds");

        foreach ($resp as $item) {
            $items[] = new Refund($this->api, $item);
        }

        return $items;
    }
}
