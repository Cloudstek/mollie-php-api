<?php

namespace Mollie\API\Resource;

use Mollie\API\Model\Customer;
use Mollie\API\Resource\Base\CustomerResourceBase;
use Mollie\API\Resource\Customer\PaymentResource as CustomerPaymentResource;
use Mollie\API\Resource\Customer\MandateResource as CustomerMandateResource;
use Mollie\API\Resource\Customer\SubscriptionResource as CustomerSubscriptionResource;

class CustomerResource extends CustomerResourceBase
{
    /**
     * Get customer
     *
     * @param Customer|string $customerId
     * @return Customer
     */
    public function get($customerId = null)
    {
        // Get customer ID
        $customerId = $this->getCustomerID($customerId);

        // Get customer
        $resp = $this->api->request->get("/customers/{$customerId}");

        // Return customer model
        return new Customer($this->api, $resp);
    }

    /**
     * Get all customers
     * @return Customer[]
     */
    public function all()
    {
        $items = [];

        // Get all customers
        $resp = $this->api->request->getAll("/customers");

        if (!empty($resp) && is_array($resp)) {
            foreach ($resp as $item) {
                $items[] = new Customer($this->api, $item);
            }
        }

        return $items;
    }

    /**
     * Create customer
     *
     * @see https://www.mollie.com/nl/docs/reference/customers/create
     * @param string $name Customer name
     * @param string $email Customer email
     * @param array|object $metadata Metadata for this customer
     * @return Customer
     */
    public function create($name, $email, $metadata = null)
    {
        // Check metadata type
        if (!is_object($metadata) && !is_array($metadata)) {
            throw new \InvalidArgumentException('Metadata argument must be of type array or object.');
        }

        // Create customer
        $resp = $this->api->request->post("/customers", [
            'name'      => $name,
            'email'     => $email,
            'locale'    => $this->api->getLocale(),
            'metadata'  => $metadata,
        ]);

        // Return customer model
        return new Customer($this->api, $resp);
    }

    /**
     * Update customer details
     *
     * @see https://www.mollie.com/nl/docs/reference/customers/update
     * @param string $name Customer name
     * @param string $email Customer email
     * @param array|object $metadata Metadata for this customer
     * @param Customer|string $customerId
     * @throws \BadMethodCallException
     * @return Customer
     */
    public function update($name = null, $email = null, $metadata = null, $customerId = null)
    {
        // Check metadata type
        if (!is_object($metadata) && !is_array($metadata)) {
            throw new \InvalidArgumentException('Metadata argument must be of type array or object.');
        }

        // Get customer ID
        $customerId = $this->getCustomerID($customerId);

        // Build parameter list
        $params = [];

        if (!empty($name)) {
            $params['name'] = $name;
        }
        if (!empty($email)) {
            $params['email'] = $email;
        }
        if (!empty($metadata)) {
            $params['metadata'] = $metadata;
        }

        // Check parameters for at least one field to update
        if (empty($params)) {
            throw new \BadMethodCallException("No arguments supplied, please provide either name, email or metadata.");
        }

        // Set locale
        $params['locale'] = $this->api->getLocale();

        // Update customer
        $resp = $this->api->request->post("/customers/{$customerId}", $params);

        // Return customer model
        return new Customer($this->api, $resp);
    }

    /**
     * Customer payment resource
     * @return CustomerPaymentResource
     */
    public function payment()
    {
        if (empty($this->customer)) {
            throw new \BadMethodCallException("No customer ID was given");
        }

        return new CustomerPaymentResource($this->api, $this->customer);
    }

    /**
     * Customer mandate resource
     *
     * @param Mollie\API\Model\Mandate|string $mandate
     * @return CustomerMandateResource
     */
    public function mandate($mandate = null)
    {
        if (empty($this->customer)) {
            throw new \BadMethodCallException("No customer ID was given");
        }

        return new CustomerMandateResource($this->api, $this->customer, $mandate);
    }

    /**
     * Customer subscription resource
     *
     * @param Mollie\API\Model\Subscription|string $subscription
     * @return CustomerSubscriptionResource
     */
    public function subscription($subscription = null)
    {
        if (empty($this->customer)) {
            throw new \BadMethodCallException("No customer ID was given");
        }

        return new CustomerSubscriptionResource($this->api, $this->customer, $subscription);
    }
}
