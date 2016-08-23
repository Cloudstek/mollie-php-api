<?php

namespace Mollie\API\Base;

abstract class RequestBase
{
    abstract public function get($uri, array $parameters = []);
    abstract public function getAll($uri);
    abstract public function post($uri, $data);
    abstract public function delete($uri);
}
