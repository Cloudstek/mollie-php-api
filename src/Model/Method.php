<?php

namespace Mollie\API\Model;

use Mollie\API\Model\Base\ModelBase;

/**
 * Method model
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Method extends ModelBase
{
    /** @var string Payment method ID */
    public $id;

    /** @var string Full name of the payment method */
    public $description;

    /** @var object Object with minimum and maximum allowed payment amount */
    public $amount;

    /** @var object URLs of images representing the payment method. */
    public $image;

    /**
     * Parse data and convert it's value when needed e.g. parse dates into their respective objects
     *
     * @param string $name Variable name
     * @param mixed $value
     * @return mixed
     */
    protected function parseData($name, $value)
    {
        if ($name == 'amount' && !empty($value) && is_object($value)) {
            $value->minimum += 0;
            $value->maximum += 0;
        }

        return $value;
    }

    /**
     * Get HTML representation of payment method image
     *
     * @param string $size Image size (normal or bigger)
     * @see https://www.mollie.com/nl/docs/reference/methods/get
     * @return string
     */
    public function image($size = 'normal')
    {
        if (!property_exists($this->image, $size)) {
            throw new \InvalidArgumentException("Image size '{$size}' for payment method {$this->id} does not exist.");
        }

        return sprintf('<img src="%s" alt="%s">', $this->image->$size, $this->description);
    }

    /**
     * Minimum allowed payment amount in EURO for this payment method
     * @return double
     */
    public function getMinimumAmount()
    {
        return $this->amount->minimum;
    }

    /**
     * Maximum allowed payment amount in EURO for this payment method
     * @return double
     */
    public function getMaximumAmount()
    {
        return $this->amount->maximum;
    }
}
