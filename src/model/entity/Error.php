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
 * Class representing a generic RESTful API error object.
 *
 * @package Lime\ExpressStatement\Model\Entity
 */
class Error extends Serializable {

    /**  @var string Error code. */
    public $code;

    /** @var string Error description. */
    public $description;

    /** @var string Localized error description. */
    public $localizedDescription;

    /**
     * Error constructor.
     * @param $code string Error code.
     * @param $description string Error description in English.
     * @param $localizedDescription string Localized error description, if available.
     */
    public function __construct($code = "ERROR_GENERIC", $description = "", $localizedDescription = "")
    {
        $this->code = $code;
        $this->description = $description;
        $this->localizedDescription = $localizedDescription;
    }


}