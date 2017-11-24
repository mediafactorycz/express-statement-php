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

namespace Lime\ExpressStatement\Model\Base;

use DateTimeZone;

/**
 * Subclass of DateTime class that (de)serializes from/to date represented as ISO 8601 string.
 *
 * @package Lime\ExpressStatement\Model\Base
 */
class ISO8601DateTime extends \DateTime implements \JsonSerializable {

    /**
     * Override of the createFromFormat provided by DateTime, to provide correct return type
     * @param $format String Date format
     * @param $time String Time instance
     * @param $object Object
     * @return static
     */
    static public function createFromFormat($format, $time, $object = NULL) {
        $dateTime = new static();
        $dateTime->setTimestamp(parent::createFromFormat($format, $time, $object)->getTimestamp());
        return $dateTime;
    }

    /**
     * Create a new instance from provided string that represents date as ISO 8601 format.
     *
     * @param $time string String representing date in ISO 8601.
     * @param DateTimeZone|null $timezone Timezone.
     * @return ISO8601DateTime Object representing a date time.
     */
    public static function createFromISO8601Format(string $time, DateTimeZone $timezone = null): ISO8601DateTime {
        return ISO8601DateTime::createFromFormat('Y-m-d\TH:i:s+', $time, $timezone);;
    }

    /**
     * Return "now" timestamp as ISO 8601 formatted string.
     *
     * @return string ISO 8601 string representing "now".
     */
    public static function nowAsString(): string {
        return (new ISO8601DateTime())->jsonSerialize();
    }

    /**
     * Render the object as ISO 8601 string.
     *
     * @return string JSON representation of date, string in ISO 8601 format.
     */
    public function jsonSerialize(): string {
        return $this->format("c");
    }

}