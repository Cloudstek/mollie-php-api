<?php

namespace Mollie\API\Resource\Base;

use Mollie\API\Mollie;

abstract class ResourceBase
{
    /** @var Mollie */
    protected $api;

    /** @var string */
    protected $id;

    /**
     * Constructor
     *
     * @param Mollie $api Mollie API reference
     * @param string $id Resource ID
     */
    public function __construct(Mollie $api, $id = null)
    {
        $this->api = $api;
        $this->id = $id;
    }

    /**
     * Get resource ID from string or model
     *
     * For example:
     * <code>
     * <?php
     *     $mollie = new Mollie('api_key');
     *     $customer = $mollie->customer('cst_test')->get()     // call using global defined customer
     *     $customer = $mollie->customer()->get('cst_test')     // call using local defined customer
     *     $customer = $mollie->customer()->get()               // Error! No global or local customer defined
     * ?>
     * </code>
     * @param ModelBase|string $resource Model or string containing the resource ID
     * @param string $type Full class reference
     * @param string $property Resource ID property for resource
     * @return string
     */
    protected function _getResourceID($resource, $type, &$property = null)
    {
        // Get short class name
        $name = strtolower(substr($type, strrpos($type, '\\') + 1));

        // Check local resource ID
        if (!empty($resource)) {
            if ($resource instanceof $type) {
                return $resource->id;
            } elseif (is_string($resource)) {
                return $resource;
            } else {
                throw new \InvalidArgumentException(sprintf("%s argument must either be a %s object or an ID as string.", ucfirst($name), $type));
            }
        } elseif (isset($property) && !empty($property)) {
            // Return global resource ID
            return $property;
        } elseif (!empty($this->id)) {
            // Return global resource ID
            return $this->id;
        } else {
            // No local or global resource ID
            throw new \BadMethodCallException("No {$name} ID was given");
        }
    }
}
