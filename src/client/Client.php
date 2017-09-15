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

/**
 * Client to the Express Statement service.
 *
 * @package Lime\ExpressStatement\Client
 */
class Client {

    /** @var string Client library version. */
    private static $CLIENT_VERSION = "1.8.1";

    /** @var string Default endpoint URL. */
    private static $BASE_URL = "https://service.rychlyvypis.cz";

    /** @var string APP_KEY value. */
    private $appKey;

    /** @var string APP_PRIVATE_KEY value. */
    private $appPrivateKey;

    /** @var string SERVER_PUBLIC_KEY value. */
    private $serverPublicKey;

    /**
     * Client constructor.
     *
     * @param $appKey string Application key, delivered as "APP_KEY" value.
     * @param $appPrivateKey string Application private key, delivered as "APP_PRIVATE_KEY" value.
     * @param $serverPublicKey string Server public key, delivered as "SERVER_PUBLIC_KEY" value.
     */
    public function __construct($appKey, $appPrivateKey, $serverPublicKey)
    {
        $this->appKey = $appKey;
        $this->appPrivateKey = $appPrivateKey;
        $this->serverPublicKey = $serverPublicKey;
    }

    public function initExpressStatement() {

    }

    public function fetchConnectedBankList($sessionId, $sessionPublicKey) {

    }

    public function deleteAllConnectionsForBank($sessionId, $bic, $sessionPublicKey) {

    }

    public function deleteAllConnections($sessionId, $sessionPublicKey) {

    }

    public function getExpressStatement($sessionId, $sessionPublicKey) {

    }


}