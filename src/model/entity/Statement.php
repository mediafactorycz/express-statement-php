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
 * Class representing a bank account statement.
 *
 * @package Lime\ExpressStatement\Model\Entity
 */
class Statement extends Serializable {

    /** @var AccountIdentification Account identification for the account on the statement. */
    public $account;

    /** @var AccountBalance Balance on the account at the end of the statement period. */
    public $balance;

    /** @var StatementPeriod Period (from-to) of this statement. */
    public $period;

    /** @var Transaction[] List of transactions on the account. */
    public $transactions;

    public function typeHint($fieldName) {
        if ($fieldName === "account") {
            return new AccountIdentification();
        } else if ($fieldName === "balance") {
            return new AccountBalance();
        } else if ($fieldName === "period") {
            return new StatementPeriod();
        } else if ($fieldName === "transactions") {
            return array();
        } else if ($fieldName === "transactions[]") {
            return new Transaction();
        }
        return parent::typeHint($fieldName);
    }

}