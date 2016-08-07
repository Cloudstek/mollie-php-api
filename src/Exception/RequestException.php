<?php

namespace Mollie\API\Exception;

class RequestException extends \Exception
{
    /** @var mixed $response */
    private $response;

    /**
     * Request exception constructor
     *
     * @param string $message
     * @param int $code
     * @param string|null $url
     * @param mixed|null $response
     */
    public function __construct($message, $code = 0, $url = "", $response = null)
    {
        // Save original response
        $this->response = $response;

        // New exception message
        $newMessage = "";

        // Add code
        if ($code <> 0) {
            $newMessage = "[{$code}]";
        }

        // Add url
        if (!empty($url)) {
            $newMessage .= "[{$url}]: ";
        }

        // Add message
        $newMessage .= $message;

        // Add error message from response
        if (!empty($response) && !empty($response->body->error)) {
            $newMessage .= ": {$response->body->error->message}.";
        }

        // Construct exception
        parent::__construct($newMessage, $code);
    }

    /**
     * Get response that threw the exception
     * @return mixed|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
