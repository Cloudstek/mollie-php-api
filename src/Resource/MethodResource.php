<?php

namespace Mollie\API\Resource;

use Mollie\API\Model\Method;

class MethodResource extends Base\ResourceBase
{
    /**
     * Get payment method
     *
     * @param string|null $id Payment method ID
     * @return Method
     */
    public function get($id = null)
    {
        // Get method ID
        $id = $this->_getResourceID($id, Method::class);

        $resp = $this->api->request->get("/methods/{$id}");

        // Return method model
        return new Method($this->api, $resp);
    }

    /**
     * Get all payment methods
     * @return Method[]
     */
    public function all()
    {
        $items = [];

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
