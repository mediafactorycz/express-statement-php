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
composer require lime-company/express-statement-php
```

## Usage

You can easily use our client class to call the service methods.

First, you need to initialize the client class:

```php
use Lime\ExpressStatement\Client\Client;

$client = new Client($APP_KEY, $APP_PRIVATE_KEY, $SERVER_PUBLIC_KEY);
```

The values provided to the `Client` instance are provided as they are received from the Express Statement service, no prior conversion or processing is needed:

- `$APP_KEY` - String starting with `app_` prefix.
- `$APP_PRIVATE_KEY` - String in Base64 encoding, representing the application private key.
- `$SERVER_PUBLIC_KEY` - String in Base64 encoding, representing the server public key.

After that, you can call methods easily.

### Initiate Session

In order to pair your customer with bank account statement data, you need to ask our service for a temporary pairing account (called "session") first. The temporary account is valid for 30 minutes, or until you delete all connections. 

```php
$initResponse = $client->initExpressStatement();
```

The response contains:

- `$sessionId` - Identifier of the pairing account (session). Do not send this value to front end applications. Store it internally, for example in session.
- `$sessionPublicKey` - Use this key to validate all further requests. Do not send this value to front end applications, store it internally, for example in session.
- `$redirectId` - Use this value for the purpose of redirection, in the browser redirect.
- _technical attributes_

### Fetch Available Accounts

After you have your session established, you can ask for the connection status on that session.

```php
$getBankListResponse = $client->fetchConnectedBankList($sessionId, $sessionPublicKey);
```

The response contains:

- `$banks` - Banks that are connected with the session.
- `$availableBanks` - Banks that are still available for connection.
- `$expires` - Timestamp when the session expires and related pairing is no longer available.
- _technical attributes_

### Fetch Bank Account Statement

After user connects one or more banks in the web application, session contains association to these banks. You can now fetch the bank account statement data, like so:

```php
$expressStatementResponse = $client->getExpressStatement($sessionId, $sessionPublicKey);
```

The response contains structured data of bank account statements. You can use it in the object form, or you can store the data for later:

```php
$json = $expressStatementResponse->serialize();
```

### Remove Pairing With Single Bank

You can disconnect any bank from the session easily, by providing the bank's BIC value:

```php
$client->deleteAllConnectionsForBank($sessionId, $bic, $sessionPublicKey);
```

### Remove Session

You can also disconnect all banks from the session and clear the session:

```php
$client->deleteAllConnections($sessionId, $sessionPublicKey);
```

## License

This project is licensed under Apache 2.0 license. See the LICENSE file for details.

## Contact

In case of an emergency, use support@lime-company.eu e-mail address.