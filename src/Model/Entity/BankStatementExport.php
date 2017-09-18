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

namespace Lime\ExpressStatement\Model\Entity;

use Lime\ExpressStatement\Model\Base\Serializable;

/**
 * Class representing an bank account statement export from a single bank.
 *
 * @package Lime\ExpressStatement\Model\Entity
 */
class BankStatementExport extends Serializable {

    /** @var Bank Bank information for the bank connected to the statement export */
    public $bank;

    /** @var Statement[] List of bank account statements */
    public $statements;

    public function typeHint(string $fieldName){
        if ($fieldName === "bank") {
            return new Bank();
        } else if ($fieldName === "statements") {
            return array();
        } else if ($fieldName === "statements[]") {
            return new Statement();
        }
        return parent::typeHint($fieldName);
    }

}