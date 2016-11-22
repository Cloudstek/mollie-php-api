<?php

namespace Mollie\API\Resource;

use Mollie\API\Model\Method;
use Mollie\API\Resource\Base\ResourceBase;

class MethodResource extends ResourceBase
{
    /**
     * Get payment method
     *
     * @param string|null $methodId Payment method ID
     * @return Method
     */
    public function get($methodId = null)
    {
        // Get method ID
        $methodId = $this->getResourceID($methodId, Method::class);

        $resp = $this->api->request->get("/methods/{$methodId}");

        // Return method model
        return new Method($this->api, $resp);
    }

    /**
     * Get all payment methods
     * @return Method[]
     */
    public function all()
    {
        $items = array();

        // Get all methods
        $resp = $this->api->request->getAll("/methods");

        if (!empty($resp) && is_array($resp)) {
            foreach ($resp as $item) {
                $items[] = new Method($this->api, $item);
            }
        }

        return $items;
    }
}
