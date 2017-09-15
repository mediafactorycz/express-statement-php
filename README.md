# Express Statement Client for PHP

This repository contains an easy to use PHP client for the Express Statement service (https://rychlyvypis.cz/).

## Prerequisites

- PHP 7.1
- Configuration values from the Express Statement service (contact us to obtain them)
    - `APP_KEY`
    - `APP_PRIVATE_KEY`
    - `SERVER_PUBLIC_KEY`

## Installation

Use our `composer` package:

```sh
composer require lime/express-statement-php
```

## Usage

You can easily use our client class to call the service methods.

First, you need to initialize the client class:

```php
use Lime\ExpressStatement\Client\Client;

$client = new Client($APP_KEY, $APP_PRIVATE_KEY, $SERVER_PUBLIC_KEY);
```

After that, you can call methods easily:

```php
$initResponse = $client->initExpressStatement();
```

## License

This project is licensed under Apache 2.0 license. See the LICENSE file for details.

## Contact

In case of an emergency, use support@lime-company.eu e-mail address.