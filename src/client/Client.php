<?php
/**
 * Copyright 2017 Lime - HighTech Solutions s.r.o.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Lime\ExpressStatement\Client;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Lime\ExpressStatement\Crypto\KeyConverter;
use Lime\ExpressStatement\Crypto\Signature;
use Lime\ExpressStatement\Model\Base\ISO8601DateTime;
use Lime\ExpressStatement\Model\Base\Serializable;
use Lime\ExpressStatement\Model\Entity\Error;
use Lime\ExpressStatement\Model\Exception\ErrorException;
use Lime\ExpressStatement\Model\Exception\SignatureFailedException;
use Lime\ExpressStatement\Model\Request\DeleteBankConnectionRequest;
use Lime\ExpressStatement\Model\Request\DeleteConnectionRequest;
use Lime\ExpressStatement\Model\Response\CreateStatementAccountResponse;
use Lime\ExpressStatement\Model\Response\DeleteBankConnectionResponse;
use Lime\ExpressStatement\Model\Response\DeleteConnectionResponse;
use Lime\ExpressStatement\Model\Response\ErrorResponse;
use Lime\ExpressStatement\Model\Response\GetLinkedAccountListResponse;
use Lime\ExpressStatement\Model\Response\GetStatementResponse;
use Mdanter\Ecc\Crypto\Key\PublicKey;

/**
 * Client to the Express Statement service.
 *
 * @package Lime\ExpressStatement\Client
 */
class Client {

    /** Client library version. */
    const VERSION = "1.9.0";

    /** @var string Default endpoint URL. */
    public $BASE_URL = "https://service.rychlyvypis.cz";

    /** @var string APP_KEY value. */
    private $appKey;

    /** @var string APP_PRIVATE_KEY value. */
    private $appPrivateKey;

    /** @var string SERVER_PUBLIC_KEY value. */
    private $serverPublicKey;

    /** @var \GuzzleHttp\Client Underlying HTTP client. */
    private $client;

    /** @var Signature Class used for underlying signature computing and validation. */
    private $signature;

    /** @var KeyConverter Class used for converting between Base64 encoded key data and actual object key representation. */
    private $converter;

    /**
     * Client constructor.
     *
     * @param $appKey string Application key, delivered as "APP_KEY" value.
     * @param $appPrivateKey string Application private key, delivered as "APP_PRIVATE_KEY" value.
     * @param $serverPublicKey string Server public key, delivered as "SERVER_PUBLIC_KEY" value.
     */
    public function __construct(string $appKey, string $appPrivateKey, string $serverPublicKey) {
        // Initialize internal objects
        $this->converter = new KeyConverter();
        $this->signature = new Signature();

        // Initialize the client with provided information - APP_KEY, APP_PRIVATE_KEY, SERVER_PUBLIC_KEY
        $this->appKey = $appKey;
        $this->appPrivateKey = $this->converter->bytesToPrivateKey(base64_decode($appPrivateKey));
        $this->serverPublicKey = $this->converter->bytesToPublicKey(base64_decode($serverPublicKey));

        // Prepare a HTTP client
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * Initializes the express statement process by a requesting a new express
     * statement identifier ("session ID").
     *
     * @return Serializable|CreateStatementAccountResponse New response containing a new session ID and a session public key.
     */
    public function initExpressStatement(): CreateStatementAccountResponse {
        // Prepare request parameters for query
        $params = array();
        $params["appKey"] = $this->appKey;
        $params["timestamp"] = ISO8601DateTime::nowAsString();
        $params["nonce"] = $this->signature->nonce();

        return $this->httpGet("/api/statement/id", $params, $this->serverPublicKey, new CreateStatementAccountResponse());
    }

    /**
     * Get the list of connected banks with given session ID.
     *
     * @param $sessionId string Session identifier.
     * @param $sessionPublicKey string A public key of this session as Base64 string, obtained by calling 'initExpressStatement' method.
     * @return Serializable|GetLinkedAccountListResponse Response with a list of currently linked banks and list of available banks.
     */
    public function fetchConnectedBankList(string $sessionId, string $sessionPublicKey): GetLinkedAccountListResponse {
        // Prepare request parameters for query
        $params = array();
        $params["appKey"] = $this->appKey;
        $params["timestamp"] = ISO8601DateTime::nowAsString();
        $params["nonce"] = $this->signature->nonce();
        $params["sessionId"] = $sessionId;

        // Decode and instantiate the session public key.
        $publicKey = $this->converter->bytesToPublicKey(base64_decode($sessionPublicKey));

        return $this->httpGet("/api/statement/bank/list", $params, $publicKey, new GetLinkedAccountListResponse());
    }

    /**
     * Delete a connection to given bank for given session ID.
     *
     * @param $sessionId string Session identifier.
     * @param $bic string BIC code of the bank to be disconnected from the statement.
     * @param $sessionPublicKey string A public key of this session as Base64 string, obtained by calling 'initExpressStatement' method.
     * @return Serializable|DeleteBankConnectionResponse OK response with session ID and BIC code in case everything works as expected.
     */
    public function deleteAllConnectionsForBank(string $sessionId, string $bic, string $sessionPublicKey): DeleteBankConnectionResponse {
        // Prepare request object for the call
        $request = new DeleteBankConnectionRequest();
        $request->appKey = $this->appKey;
        $request->bic = $bic;
        $request->nonce = $this->signature->nonce();
        $request->sessionId = $sessionId;
        $request->timestamp = ISO8601DateTime::nowAsString();

        // Decode and instantiate the session public key.
        $publicKey = $this->converter->bytesToPublicKey(base64_decode($sessionPublicKey));

        return $this->httpPost("/api/statement/bank/delete", $request, $publicKey, new DeleteBankConnectionResponse());
    }

    /**
     * Delete connections to all connected banks for given session ID.
     *
     * @param $sessionId string Session identifier.
     * @param $sessionPublicKey string A public key of this session as Base64 string, obtained by calling 'initExpressStatement' method.
     * @return Serializable|DeleteConnectionResponse OK response with session ID in case everything works as expected.
     */
    public function deleteAllConnections(string $sessionId, string $sessionPublicKey): DeleteConnectionResponse {
        // Prepare request object for the call
        $request = new DeleteConnectionRequest();
        $request->appKey = $this->appKey;
        $request->nonce = $this->signature->nonce();
        $request->sessionId = $sessionId;
        $request->timestamp = ISO8601DateTime::nowAsString();

        // Decode and instantiate the session public key.
        $publicKey = $this->converter->bytesToPublicKey(base64_decode($sessionPublicKey));

        return $this->httpPost("/api/statement/delete", $request, $publicKey, new DeleteConnectionResponse());
    }

    /**
     * Fetch express statement data for all banks that are connected with a
     * given session ID at the moment.
     *
     * @param $sessionId string Session identifier.
     * @param $sessionPublicKey string A public key of this session as Base64 string, obtained by calling 'initExpressStatement' method.
     * @return Serializable|GetStatementResponse Express statement data for all connected banks.
     */
    public function getExpressStatement(string $sessionId, string $sessionPublicKey): GetStatementResponse {
        // Prepare request parameters for query
        $params = array();
        $params["appKey"] = $this->appKey;
        $params["timestamp"] = ISO8601DateTime::nowAsString();
        $params["nonce"] = $this->signature->nonce();
        $params["sessionId"] = $sessionId;

        // Decode and instantiate the session public key.
        $publicKey = $this->converter->bytesToPublicKey(base64_decode($sessionPublicKey));

        return $this->httpGet("/api/statement/export", $params, $publicKey, new GetStatementResponse());
    }

    // Private methods

    /**
     * Send HTTP request with provided HTTP method to endpoint defined by given path, with HTTP request params.
     * Also includes handling of private key signature of normalized request data and signature validation of
     * response object using a public key. Method serializes response into the provided object instance.
     *
     * @param string $method HTTP method, for example POST or GET.
     * @param string $path Request path, for example '/api/statement/export'. Parameter will be appended to
     * base URL. The path must start with '/' character.
     * @param array $params Request parameters. For POST method, this is usually an array with 'body' key. For
     * GET method, this is usually an array with 'query' key. This array will be automatically extended with
     * array with 'header' key, that represents HTTP headers required for all HTTP requests.
     * @param string $normalizedData Normalized data for computing request signature. For GET method, this is
     * a signature base string computed by sorting query parameters by key and concatenating them to query
     * string. For POST method, these are raw request data.
     * @param PublicKey $publicKey Public key used for response signature verification.
     * @param Serializable $response Instance of the response object, it will be initialized using the data
     * from the JSON response.
     * @return Serializable Response object - instance of $response with values from the JSON response.
     * @throws ErrorException In case HTTP processing occurs, or server returns error response.
     * @throws SignatureFailedException In case server response signature does not match.
     */
    private function http(string $method, string $path, array $params, string $normalizedData, PublicKey $publicKey, Serializable $response): Serializable {

        // Prepare request parameters
        $url = $this->BASE_URL . $path;
        $signRequest = $this->signature->computeDataSignature($normalizedData, $this->appPrivateKey);

        // Add HTTP headers to params
        $params["headers"] = [
            "Accept" => 'application/json',
            "Content-Type" => 'application/json',
            "User-Agent" => 'ExpressStatement/' . Client::VERSION,
            "X-Data-Signature" => base64_encode($signRequest)
        ];

        // Send GET request to provided path
        try {
            $res = $this->client->request($method, $url, $params);
        } catch (ConnectException $ex) { // Networking has failed
            $error = new Error(Error::ERROR_NETWORK, $ex->getMessage());
            throw new ErrorException([ $error ]);
        } catch (ClientException $ex) { // Client error - HTTP 400
            $body = $ex->getResponse()->getBody()->getContents();
            $errorResponse = new ErrorResponse();
            $errorResponse->unserialize($body);
            throw new ErrorException($errorResponse->errors);
        } catch (RequestException $ex) { // Other HTTP errors
            $error = new Error(Error::ERROR_NETWORK, $ex->getMessage());
            throw new ErrorException([ $error ]);
        }

        // Fetch response data
        $body = $res->getBody()->getContents();

        // Process the response
        $signResponse = $res->getHeaderLine("X-Data-Signature");
        if ($this->signature->validateDataSignature($body, base64_decode($signResponse), $publicKey)) {
            $response->unserialize($body);
            return $response;
        } else {
            throw new SignatureFailedException();
        }
    }

    /**
     * Send correctly configured HTTP GET request to provided path with query parameters.
     *
     * @param string $path Path to be used for the request.
     * @param array $params Request query parameters.
     * @param PublicKey $publicKey Public key used for response signature validation.
     * @param Serializable $response Instance for the response object.
     * @return Serializable Response, $response value with data form response JSON.
     */
    private function httpGet(string $path, array $params, PublicKey $publicKey, Serializable $response): Serializable {
        return $this->http("GET", $path, [ 'query' => $params ], HttpUtil::normalizeQueryParameterMap($params), $publicKey, $response);
    }

    /**
     * Send correctly configured HTTP POST request to provided path with a request object.
     *
     * @param string $path Path to be used for the request.
     * @param Serializable $request Request object.
     * @param PublicKey $publicKey Public key used for response signature validation.
     * @param Serializable $response Instance for the response object.
     * @return Serializable Response, $response value with data form response JSON.
     */
    private function httpPost(string $path, Serializable $request, PublicKey $publicKey, Serializable $response): Serializable {
        $requestBody = $request->serialize();
        return $this->http("POST", $path, [ "body" => $requestBody ], $requestBody, $publicKey, $response);
    }


}