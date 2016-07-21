<?php

namespace Mollie\API\Model;

use Mollie\API\Base\ModelBase;

class Issuer extends ModelBase {

    /** @var string Issuer ID */
    public $id;

    /** @var string Issuer's full name */
    public $name;

    /** @var string Payment method the issuer belongs to */
    public $method;
}
