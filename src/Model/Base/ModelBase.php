<?php

namespace Mollie\API\Model\Base;

use Mollie\API\Mollie;

/**
 * Model base
 */
abstract class ModelBase
{
    /** @var object */
    protected $response;

    /** @var Mollie */
    protected $api;

    /** @var string Resource type */
    public $resource;

    /**
     * Constructor
     *
     * @param Mollie $api
     * @param object|array $data
     */
    public function __construct(Mollie $api, $data)
    {
        // Fill model
        $this->fill($data);

        // API reference
        $this->api = $api;
    }

    /**
     * Fill model with data
     *
     * @param array|object|\Traversable $data
     * @throws InvalidArgumentException
     */
    protected function fill($data)
    {
        // Check data type
        if (!is_object($data) && !is_array($data) && !$data instanceof \Traversable) {
            throw new \InvalidArgumentException("Model data should be an array, object or traversable object.");
        }

        // Fill model
        foreach ($data as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $this->parseData($k, $v);
            }
        }

        // Set raw response data and normalize to object
        $this->response = is_object($data) ? $data : json_decode(json_encode($data, JSON_FORCE_OBJECT), false);
    }

    /**
     * Parse data and convert it's value when needed e.g. parse dates into their respective objects
     *
     * @param string $name Variable name
     * @param mixed $value
     * @return mixed
     */
    protected function parseData($name, $value)
    {
        if (!empty($value)) {
            if (preg_match('/.+(Datetime|Date)$/', $name)) {
                // ISO 8601 Date
                try {
                    return new \DateTime($value);
                } catch (\Exception $ex) {
                    throw new \InvalidArgumentException("Property {$name} is not a valid date/time string: {$value}.");
                }
            } elseif (preg_match('/.+(Period)$/', $name)) {
                // ISO 8601 Duration
                try {
                    return new \DateInterval($value);
                } catch (\Exception $ex) {
                    throw new \InvalidArgumentException("Property {$name} is not a valid ISO 8601 duration string: {$value}.");
                }
            }
        }

        if ($name == "metadata") {
            // Metadata
            if (is_string($value)) {
                // Try to parse as JSON string
                $jsonVal = json_decode($value);

                if (json_last_error() == JSON_ERROR_NONE) {
                    return $jsonVal;
                }
            }

            // If not JSON, check if it's an object or array
            if (!$value instanceof \stdClass && !is_array($value)) {
                throw new \InvalidArgumentException("Property {$name} is not an object, array or valid JSON string.");
            }
        } elseif (preg_match('/amount.*$/', $name) && isset($value)) {
            // Amount
            return $value + 0.0;
        }

        return $value;
    }

    /**
     * Get JSON response object
     * @return object
     */
    public function getResponse()
    {
        return $this->response;
    }
}
