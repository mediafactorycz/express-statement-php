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


/**
 * Interface representing object that can be serialized from and to JSON.
 * @package Lime\Model\Base
 */
class Serializable implements \Serializable {

    /**
     * Get sanitized value from a mixed object.
     *
     * @param $mixed mixed Mixed to read the value from.
     * @param $property string Property to read from $mixed.
     * @return object|null Value of $mixed->{$property} if it can be returned, null otherwise.
     */
    private function getValue($mixed, $property) {
        if (isset($mixed->{$property})) {
            return $mixed->{$property};
        } else {
            return null;
        }
    }

    /**
     * Return value of a class used by assigning a value into expected type class,
     * in case of arrays with type hints provided by given property.
     *
     * @param $value object Value to be assigned.
     * @param $expectedType object Object to be filled in.
     * @param $property string Property to be used for type hinting in case of arrays.
     * @return array|bool|\DateTime|object|null Assigned object instance.
     */
    private function assignImpl($value, $expectedType, $property) {

        // No type hint present, just assign...
        if ($expectedType === null) {
            return $value;
        }

        // Serialize date and time
        else if (is_subclass_of($expectedType, \DateTime::class)) {
            return ISO8601DateTime::createFromISO8601Format($value);
        }

        // Serializable instance, recursively serialize.
        else if (is_subclass_of($expectedType, Serializable::class)) {
            $expectedType->initWithMixed($value);
            return $expectedType;
        }

        // Array, ask for type hint for elements inside and serialize the array.
        else if (is_array($expectedType)) {
            $sourceArray = (array) $value;
            $targetArray = array();
            if ($sourceArray && count($sourceArray) > 0) {
                foreach ($sourceArray as $item) {
                    $prop = strtolower($property . "[]");
                    $expectedArrayItemType = $this->typeHint($prop);
                    $obj = $this->assignImpl($item, $expectedArrayItemType, $prop);
                    $targetArray[] = $obj;
                }
            }
            return $targetArray;
        }

        // Fallback implementation
        return null;
    }

    /**
     * Initialize object instance with mixed instance.
     *
     * @param $mixed mixed instance to be used for initialization.
     */
    public function initWithMixed($mixed) {

        // Return on null
        if ($mixed === null) {
            return;
        }

        // Get object properties
        $properties = get_object_vars($this);

        foreach ($properties as $property => $val) {

            // Get sanitized object value
            $value = $this->getValue($mixed, $property);

            // Get possible type hints
            $expectedType = $this->typeHint(strtolower($property));

            // Assign the value to the expected type
            $this->{$property} = $this->assignImpl($value, $expectedType, $property);

        }
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize() {
        return json_encode($this);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized) {
        $mixed = json_decode($serialized);
        $this->initWithMixed($mixed);
    }

    /**
     * Ask for a type hint for given field.
     *
     * @param $fieldName string Field name for which to provide hints. In case some
     * type hint returns 'array()', there also can be a hint for it's contents - this is
     * by convention marked with '[]' suffix. For example, if there is an array called
     * 'transactions' in the class, you may also provide hint for 'transactions[]' to specify
     * what class is used for array elements.
     * @return null No type hints are provided.
     */
    public function typeHint($fieldName) {
        return null;
    }

}