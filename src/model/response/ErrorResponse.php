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
use Lime\ExpressStatement\Model\Entity\Error;

/**
 * Generic error response.
 *
 * @package Lime\ExpressStatement\Model\Response
 */
class ErrorResponse extends Serializable {

    /**
     * @var Error[] Errors associated with the response.
     */
    public $errors;

    public function typeHint($fieldName) {
        if ($fieldName === "errors") {
            return array();
        } else if ($fieldName === "errors[]") {
            return new Error();
        }
        return parent::typeHint($fieldName);
    }

}