# Mollie PHP API client

[![Build Status](https://travis-ci.org/Cloudstek/mollie-php-api.svg?branch=master)](https://travis-ci.org/Cloudstek/mollie-php-api) [![Code Climate](https://codeclimate.com/github/Cloudstek/mollie-api-php/badges/gpa.svg)](https://codeclimate.com/github/Cloudstek/mollie-api-php) [![Test Coverage](https://codeclimate.com/github/Cloudstek/mollie-api-php/badges/coverage.svg)](https://codeclimate.com/github/Cloudstek/mollie-api-php/coverage)

Simple to use, modern and well-tested PHP API client for [Mollie](https://www.mollie.com/nl/docs/overview).

## Requirements

* PHP 5.6 or newer
* PHP cURL extension (with SSL)
* PHP xdebug extension (optional, for unit tests)
* [Composer](https://getcomposer.org)
* Active website profile at Mollie.com (see: [Mollie Documentation](https://www.mollie.com/en/docs/authentication))

## Features

Currently all API functions that don't require oauth authentication are supported. This means you can use this API client for everything except managing organizations, profiles, permissions and settlements.

* Payments
  * Payment methods
  * Issuers
  * Refunds
  * Recurring payments
* Customers
  * Mandates
  * Subscriptions

## Installation

The Mollie PHP API client is available as composer package. Installation is as simple as requiring the package for your project.

```sh
composer require cloudstek/mollie-php-api
```

You can also manually add the package to your projects composer.json requirements:

```json
{
  "require": {
    "cloudstek/mollie-php-api": "^1.0.0"
  }
}
```

Next, require the composer autoloader in your project:

```php
<?php
  require_once("vendor/autoload.php");
```

## Usage

Below are a few common examples on how to use the Mollie PHP API client. For advanced usage please see the [documentation](https://mollie-php-api.github.com/docs).

### Initializing the Mollie API client class

```php
<?php
  
  // Import the namespace
  use Mollie\API\Mollie;

  // Create an API client instance
  $mollie = new Mollie('test_yourapikeyhere');

  // Alternatively..
  $mollie = new Mollie();
  $mollie->setApiKey('test_yourapikeyhere');

  // Now you're ready to use the Mollie API, please read on for more examples.
```

### Creating a new customer

```php
<?php
  use Mollie\API\Mollie;

  // Initialize API client
  $mollie = new Mollie('test_yourapikeyhere');

  // Create customer
  $customer = $mollie->customer()->create('John Doe', 'john.doe@example.org');

  // Alternatively you can also specify a locale and/or metadata.
  // In the following example we'll create the same customer with some metadata
  $customer = $mollie->customer()->create(
    'John Doe', 'john.doe@example.org', 
    array(
      'user_id' => 11, 
      'group' => 'regular_customers'
    )
  );

  // Now save the customer ID to your database for future reference. Pseudo code:
  $db->save($customer->id);
```

### Payments

#### Regular payment

```php
<?php
  use Mollie\API\Mollie;

  // Initialize API client
  $mollie = new Mollie('test_yourapikeyhere');

  // Create new payment
  $payment = $mollie->payment()->create(10.00, 'Expensive cup of coffee', 'https://example.org/order/101');

  // Redirect user to payment page
  $payment->gotoPaymentPage();
```

#### Customer payment

According to the Mollie API documentation, Linking customers to payments enables a number of [Mollie Checkout](https://www.mollie.com/nl/checkout) features, including:

- Payment preferences for your customers.
- Enabling your customers to charge a previously used debit or credit card with a single click.
- Improved payment insights in your dashboard.
- Recurring payments.

```php
<?php
  use Mollie\API\Mollie;

  // Initialize API client
  $mollie = new Mollie('test_yourapikeyhere');

  // Create new payment
  $payment = $mollie->customer('cst_test')->payment()->create(10.00, 'Expensive cup of coffee', 'https://example.org/order/101');

  // Redirect user to payment page
  $payment->gotoPaymentPage();
```
### Recurring payments

#### Getting a recurring payment mandate

```php
<?php
  use Mollie\API\Mollie;

  // Initialize API client
  $mollie = new Mollie('test_yourapikeyhere');

  // Get customer
  $customer = $mollie->customer('cst_test')->get();

  // Make sure the customer has no valid mandates
  if(!$customer->mandate()->hasValid()) {
    // Create mandate by issueing the first recurring payment. 
    // This is usually a small amount like a few cents as it's only used to confirm 
    // the payment details.
    $customer->mandate()->createFirstRecurring(0.01, 'Recurring payment mandate confirmation', 'https://example.org/account');
  }
```

#### Create a recurring payment

```php
<?php
  use Mollie\API\Mollie;

  // Initialize API client
  $mollie = new Mollie('test_yourapikeyhere');

  // Get customer
  $customer = $mollie->customer('cst_test')->get();

  // Check if customer has a valid mandate for recurring payments
  if($customer->mandate()->hasValid()) {
    $customer->payment()->createRecurring(10.00, 'Expensive cup of coffee');
  }
  else {
    // Customer has no valid mandates, you should get one first.
  }
```

## Contributing

Feel free to make contributions to the code by submitting pull requests and opening issues to express your ideas and feature requests.

If you contribute code, make sure it is covered by unit tests and passes existing tests to prevent regressions.