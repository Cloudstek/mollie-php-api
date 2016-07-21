<?php

namespace Mollie\API\Exception;

class ModelException extends \Exception
{
    /** @var string Name of the model that threw the exception */
    private $model;

    /**
     * Model exception constructor
     *
     * @param string $message
     * @param ModelBase $model
     * @param int $code
     */
    public function __construct($message, $model, $code = 0)
    {
        $this->model = get_class($model);

        parent::__construct($message, $code);
    }

    /**
     * Get the name of the model that threw the exception
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }
}
