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

/**
 * Created by PhpStorm.
 * User: petrdvorak
 * Date: 16/09/2017
 * Time: 17:56
 */

namespace Lime\ExpressStatement\Client;


/**
 * Basic utility class for couple HTTP related routines.
 *
 * @package Lime\ExpressStatement\Client
 */
class HttpUtil {

    /**
     * Normalize GET parameters to standardized data suitable for signing.
     * @param array $params Params to be sorted.
     * @return string Normalized string suitable for signatures.
     */
    public static function normalizeQueryParameterMap(array $params) {
        ksort($params);
        $res = "";
        $firstSkipped = false;
        foreach ($params as $key => $val) {
            if ($firstSkipped) {
                $res .= "&";
            } else {
                $firstSkipped = true;
            }
            $res .= str_replace("%2B", "+", urlencode($key));
            $res .= "=";
            $res .= str_replace("%2B", "+", urlencode($val));
        }
        return $res;
    }

}