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

        // API request
        $items = $this->api->request->getAll("/refunds");

        // Yield items
        foreach ($items as $item) {
            yield new Refund($item);
        }
    }
}
