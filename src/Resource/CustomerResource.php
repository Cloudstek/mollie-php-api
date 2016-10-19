<?php

namespace Mollie\API\Resource;

use Mollie\API\Model\Customer;
use Mollie\API\Resource\Customer\PaymentResource as CustomerPaymentResource;
use Mollie\API\Resource\Customer\MandateResource as CustomerMandateResource;
use Mollie\API\Resource\Customer\SubscriptionResource as CustomerSubscriptionResource;

class CustomerResource extends Base\CustomerResourceBase
{
    /**
     * Get customer
     *
     * @param Customer|string $id
     * @return Customer
     */
    public function get($id = null)
    {
        // Get customer ID
        $id = $this->getCustomerID($id);

        // Get customer
        $resp = $this->api->request->get("/customers/{$id}");

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
     * @param array $metadata Metadata for this customer
     * @return Customer
     */
    public function create($name, $email, array $metadata = null)
    {
        // Convert metadata to JSON
        $metadata = !empty($metadata) ? json_encode($metadata) : null;

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
