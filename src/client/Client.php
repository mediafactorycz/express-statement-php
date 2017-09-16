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

    /** Default endpoint URL. */
    const BASE_URL = "https://service.rychlyvypis.cz";

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
    public function __construct($appKey, $appPrivateKey, $serverPublicKey)
    {
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
    public function initExpressStatement() {
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
     * @param $sessionPublicKey PublicKey A public key of this session as Base64 string, obtained by calling 'initExpressStatement' method.
     * @return Serializable|GetLinkedAccountListResponse Response with a list of currently linked banks and list of available banks.
     */
    public function fetchConnectedBankList($sessionId, $sessionPublicKey) {
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
     * @param $sessionPublicKey PublicKey A public key of this session as Base64 string, obtained by calling 'initExpressStatement' method.
     * @return Serializable|DeleteBankConnectionResponse OK response with session ID and BIC code in case everything works as expected.
     */
    public function deleteAllConnectionsForBank($sessionId, $bic, $sessionPublicKey) {
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
     * @param $sessionPublicKey PublicKey A public key of this session as Base64 string, obtained by calling 'initExpressStatement' method.
     * @return Serializable|DeleteConnectionResponse OK response with session ID in case everything works as expected.
     */
    public function deleteAllConnections($sessionId, $sessionPublicKey) {
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
     * @param $sessionPublicKey PublicKey A public key of this session as Base64 string, obtained by calling 'initExpressStatement' method.
     * @return Serializable|GetStatementResponse Express statement data for all connected banks.
     */
    public function getExpressStatement($sessionId, $sessionPublicKey) {
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

    private function http(string $method, string $path, array $params, string $normalizedData, PublicKey $publicKey, Serializable $response) {

        // Prepare request parameters
        $url = Client::BASE_URL . $path;
        $signRequest = $this->signature->computeDataSignature($normalizedData, $this->appPrivateKey);

        // Add HTTP headers to params
        $params["headers"] = [
            "Accept" => 'application/json',
            "Content-Type" => 'application/json',
            "User-Agent" => 'ExpressStatement/' . Client::VERSION,
            "X-DataSignature" => $signRequest
        ];

        // Send GET request to provided path
        try {
            $res = $this->client->request($method, $url, $params);
        } catch (RequestException $ex) { // Networking has failed
            $error = new Error(Error::ERROR_NETWORK, $ex->getMessage());
            throw new ErrorException([ $error ]);
        }

        // Fetch response data
        $status = $res->getStatusCode();
        $body = $res->getBody()->getContents();

        // Check if response was correct, if not, process and return error.
        if ($status !== 200) {
            $error = new Error();
            $error->unserialize($body);
            throw new ErrorException([ $error ]);
        }

        // Process the response
        $signResponse = $res->getHeader("X-Data-Signature");
        if ($this->signature->validateDataSignature($body, $signResponse, $publicKey)) {
            $response->unserialize($body);
            return $response;
        } else {
            throw new SignatureFailedException();
        }
    }

    private function httpGet(string $path, array $params, PublicKey $publicKey, Serializable $response) {
        return $this->http("GET", $path, [ 'query' => $params ], HttpUtil::normalizeQueryParameterMap($params), $publicKey, $response);
    }

    private function httpPost(string $path, Serializable $request, PublicKey $publicKey, Serializable $response) {
        $requestBody = $request->serialize();
        return $this->http("POST", $path, [ "body" => $requestBody ], $requestBody, $publicKey, $response);
    }


}