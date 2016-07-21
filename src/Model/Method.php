<?php

namespace Mollie\API\Model;

class Method extends Base\ModelBase {

    /** @var string Payment method ID */
    public $id;

    /** @var string Full name of the payment method */
    public $description;

    /** @var object Object with minimum and maximum allowed payment amount */
    public $amount;

    /** @var object URLs of images representing the payment method. */
    public $image;

    /**
     * Get HTML representation of payment method image
     *
     * @param string $size Image size (normal or bigger)
     * @see https://www.mollie.com/nl/docs/reference/methods/get
     * @return string
     */
    public function image($size = 'normal') {
        if(property_exists($this->image, $size)) {
            return sprintf('<img src="%s" alt="%s">', $this->image->$size, $this->description);
        }
    }

    /**
     * Minimum allowed payment amount in EURO for this payment method
     * @return double
     */
    public function getMinimumAmount() {
        return $this->amount->minimum;
    }

    /**
     * Maximum allowed payment amount in EURO for this payment method
     * @return double
     */
    public function getMaximumAmount() {
        return $this->amount->maximum;
    }
}
