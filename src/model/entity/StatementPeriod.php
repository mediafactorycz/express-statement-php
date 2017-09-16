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
 * Bank account statement period (from-to).
 *
 * @package Lime\ExpressStatement\Model\Entity
 */
class StatementPeriod extends Serializable {

    /** @var ISO8601DateTime From date (date more in the past, when the statement starts, for example 01 Jun). */
    public $from;

    /** @var ISO8601DateTime To date (date closer to the present, when the statement ends, for example 30 Jun). */
    public $to;

    public function typeHint($fieldName) {
        if ($fieldName === "from") {
            return new ISO8601DateTime();
        } else if ($fieldName === "to") {
            return new ISO8601DateTime();
        }
        return parent::typeHint($fieldName);
    }

}