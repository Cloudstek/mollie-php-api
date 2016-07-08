<?php

namespace Mollie\API;

interface RequestInterface {
	public function get($uri, array $parameters = []);
	public function post($uri, $data);
	public function delete($uri);
}
