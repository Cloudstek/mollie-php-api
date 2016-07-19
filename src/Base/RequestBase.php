<?php

namespace Mollie\API;

abstract class RequestBase {
	public abstract function get($uri, array $parameters = []);
	public abstract function getAll($uri);
	public abstract function post($uri, $data);
	public abstract function delete($uri);
}
