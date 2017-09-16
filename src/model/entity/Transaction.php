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

use Lime\ExpressStatement\Model\Base\ISO8601DateTime;
use Lime\ExpressStatement\Model\Base\Serializable;

/**
 * Class representing a bank account transaction.
 *
 * @package Lime\ExpressStatement\Model\Entity
 */
class Transaction extends Serializable {

    /** @var string Transaction ID, as assigned by the bank. */
    public $id;

    /** @var string Basic information about the other party. */
    public $partyInfo;

    /** @var string Description of the other party. */
    public $partyDescription;

    /** @var string IBAN number of the other party, if present. */
    public $partyIban;

    /** @var double Transaction amount. */
    public $amount;

    /** @var string Transaction currency. */
    public $currency;

    /** @var ISO8601DateTime Transaction value date (date when transaction was accounted). */
    public $valueDate;

    /** @var string Transaction description. */
    public $description;

    /** @var string Note for payee. */
    public $payeeNote;

    /** @var string Note for payer. */
    public $payerNote;

    /** @var string Variable symbol. */
    public $variableSymbol;

    /** @var string Constant symbol. */
    public $constantSymbol;

    /** @var string Specific symbol. */
    public $specificSymbol;

    public function typeHint(string $fieldName) {
        if ($fieldName === "valuedate") {
            return new ISO8601DateTime();
        }
        return parent::typeHint($fieldName);
    }

}