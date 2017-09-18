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

use Lime\ExpressStatement\Model\Base\Serializable;
use Lime\ExpressStatement\Model\Entity\BankStatementExport;

/**
 * Response object with the bank account statements for all connected banks.
 *
 * @package Lime\ExpressStatement\Model\Response
 */
class GetStatementResponse extends Serializable {

    /**
     * @var string
     */
    public $id;

    /** @var string Random 16B long cryptographic nonce, Base64 encoded */
    public $nonce;

    /**
     * @var BankStatementExport[]
     */
    public $data;

    public function typeHint(string $fieldName) {
        if ($fieldName === "data") {
            return array();
        } else if ($fieldName === "data[]") {
            return new BankStatementExport();
        }
        return parent::typeHint($fieldName);
    }

}