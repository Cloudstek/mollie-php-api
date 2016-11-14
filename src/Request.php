<?php

namespace Mollie\API;

use Httpful\Request as HttpRequest;
use Mollie\API\Exception\RequestException;

class Request extends Base\RequestBase
{
    /** @var Mollie API Instance*/
    private $api;

    /**
     * API Request constructor
     * @param Mollie $api API instance
     */
    public function __construct(Mollie $api)
    {
        $this->api = $api;
    }

    /**
     * GET Request
     *
     * @param string $uri Request URI e.g. /customer/1
     * @param array $params Request parameters
     * @throws RequestException
     * @return object
     */
    public function get($uri, array $params = [])
    {
        // API key
        $apiKey = $this->api->getApiKey();

        if (empty($apiKey)) {
            throw new RequestException('No API key entered');
        }

        // Endpoint
        $url = $this->api->getApiEndpoint($uri, $params);

        // Do request
        $resp = HttpRequest::get($url)
            ->expectsJson()
            ->withAuthorization("Bearer {$apiKey}")
            ->send();

        // Check response code
        if ($resp->code != 200) {
            throw new RequestException("Mollie API GET request failed", $resp->code, $url, $resp);
        }

        // Return response body
        if ($resp->hasBody()) {
            return $resp->body;
        }
    }

    /**
     * GET Request for retuning all items from a paginated response
     *
     * @param string $uri Request URI e.g. /customer/1
     * @return array
     */
    public function getAll($uri)
    {
        // Do request
        $resp = $this->get($uri);

        // Data
        $data = !empty($resp->data) ? $resp->data : [];
        $next = !empty($resp->links->next) ? $resp->links->next : null;

        // Get next pages (if any)
        while (!empty($next)) {
            // Request page data
            $resp = $this->get($next);

            // Append page items
            $data = array_merge($data, $resp->data);

            // Get next page link
            $next = !empty($resp->links->next) ? $resp->links->next : null;
        }

        return $data;
    }

    /**
     * POST Request
     *
     * @param string $uri Request URI e.g. /customer/1
     * @param array $data POST data
     * @throws RequestException
     * @return object
     */
    public function post($uri, $data)
    {
        // API key
        $apiKey = $this->api->getApiKey();

        if (empty($apiKey)) {
            throw new RequestException('No API key entered');
        }

        // Encode post data to JSON
        $data = json_encode($data);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Request data failed to encode to JSON format.');
        }

        // Endpoint
        $url = $this->api->getApiEndpoint($uri);

        // Do request
        $resp = HttpRequest::post($url)
            ->expectsJson()
            ->sendsJson()
            ->withAuthorization("Bearer {$apiKey}")
            ->body($data)
            ->send();

        // Check response code
        if ($resp->code != 201) {
            throw new RequestException("Mollie API POST request failed", $resp->code, $url, $resp);
        }

        // Return response body
        if ($resp->hasBody()) {
            return $resp->body;
        }
    }

    /**
     * DELETE Request
     *
     * @param string $uri Request URI e.g. /customer/1
     * @throws RequestException
     * @return object|null
     */
    public function delete($uri)
    {
        // API key
        $apiKey = $this->api->getApiKey();

        if (empty($apiKey)) {
            throw new RequestException('No API key entered');
        }

        // Endpoint
        $url = $this->api->getApiEndpoint($uri);

        // Do request
        $resp = HttpRequest::delete($url)
            ->expectsJson()
            ->withAuthorization("Bearer {$apiKey}")
            ->send();

        // Check response code
        if ($resp->code != 200 || $resp->code != 204) {
            throw new RequestException("Mollie API DELETE request failed", $resp->code, $url, $resp);
        }

        // Return response body
        if ($resp->hasBody()) {
            return $resp->body;
        }
    }
}
