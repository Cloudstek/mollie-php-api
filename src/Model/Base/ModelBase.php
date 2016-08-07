<?php

namespace Mollie\API\Model\Base;

use Mollie\API\Mollie;

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
     * @param array|object $data
     * @throws InvalidArgumentException
     */
    protected function fill($data)
    {
        // Check data type
        if (!is_object($data) && !is_array($data) && !$data instanceof \Traversable) {
            throw new \InvalidArgumentException("Model data should either be an object, array or any other traversable object.");
        }

        // Fill model
        foreach ($data as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $this->_parseData($k, $v);
            }
        }

        // Set raw response data and normalize to object
        $this->response = is_object($data) ? $data : json_decode(json_encode($data, JSON_FORCE_OBJECT), false);
    }

    /**
     * Parse data and convert it's value when needed e.g. parse dates into their respective objects (DateTime or DateInterval)
     *
     * @param string $name Variable name
     * @param mixed $value
     * @return mixed
     */
    protected function _parseData($name, $value)
    {
        if (!empty($value) && is_string($value)) {

            // ISO 8601 Date
            if (preg_match('/.+(Datetime|Date)$/', $name)) {
                try {
                    return new \DateTime($value);
                } catch (\Exception $ex) {
                    throw new \InvalidArgumentException("Property {$name} does not contain a valid ISO 8601 date/time string.");
                }
            }

            // ISO 8601 Duration
            if (preg_match('/.+(Period)$/', $name)) {
                try {
                    return new \DateInterval($value);
                } catch (\Exception $ex) {
                    throw new \InvalidArgumentException("Property {$name} does not contain a valid ISO 8601 duration string.");
                }
            }

            // JSON metadata
            if ($name == 'metadata') {
                $value = json_decode($value);

                if (json_last_error() === JSON_ERROR_NONE) {
                    return $value;
                } else {
                    throw new \InvalidArgumentException("Property {$name} does not contain valid JSON metadata.");
                }
            }

            // Amount
            if (preg_match('/amount.*$/', $name)) {
                return $value + 0.0;
            }
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
