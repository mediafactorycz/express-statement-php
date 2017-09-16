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

namespace Lime\ExpressStatement\Model\Response;

use Lime\ExpressStatement\Model\Base\ISO8601DateTime;
use Lime\ExpressStatement\Model\Base\Serializable;
use Lime\ExpressStatement\Model\Entity\Bank;

/**
 * Response object with the information about currently connected and available banks.
 *
 * @package Lime\ExpressStatement\Model\Response
 */
class GetLinkedAccountListResponse extends Serializable {

    /** @var string Session ID value. */
    public $id;

    /** @var \DateTime Timestamp indicating when the connection will expire. */
    public $expires;

    /** @var string Random 16B long cryptographic nonce, Base64 encoded. */
    public $nonce;

    /** @var Bank[] Array of banks that are already connected with provided session ID. */
    public $banks;

    /** @var Bank[] Array of banks that are still available for connection. */
    public $availableBanks;

    public function typeHint(string $fieldName) {
        if ($fieldName === "expires") {
            return new ISO8601DateTime();
        } else if ($fieldName === "banks") {
            return array();
        } else if ($fieldName === "banks[]") {
            return new Bank();
        } else if ($fieldName === "availablebanks") {
            return array();
        } else if ($fieldName === "availablebanks[]") {
            return new Bank();
        }
        return parent::typeHint($fieldName);
    }


}