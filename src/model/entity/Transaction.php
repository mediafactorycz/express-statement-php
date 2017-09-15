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

/**
 * Class representing a bank account transaction.
 * @package ExpressStatement
 */
class Transaction {

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $partyInfo;

    /**
     * @var string
     */
    public $partyDescription;

    /**
     * @var string
     */
    public $partyIban;

    /**
     * @var double
     */
    public $amount;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var \DateTime
     */
    public $valueDate;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $payeeNote;

    /**
     * @var string
     */
    public $payerNote;

    /**
     * @var string
     */
    public $variableSymbol;

    /**
     * @var string
     */
    public $constantSymbol;

    /**
     * @var string
     */
    public $specificSymbol;

}