<?php

namespace Mollie\API\Model;

class Issuer extends Base\ModelBase
{
    /** @var string Issuer ID */
    public $id;

    /** @var string Issuer's full name */
    public $name;

    /** @var string Payment method the issuer belongs to */
    public $method;

    /**
     * Issuer payment method
     * @return Method Payment method the issuer belongs to
     */
    public function method()
    {
        return $this->api->method($this->method)->get();
    }
}
