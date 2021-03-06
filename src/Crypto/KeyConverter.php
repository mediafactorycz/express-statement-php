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

namespace Lime\ExpressStatement\Crypto;

use Mdanter\Ecc\Crypto\Key\PrivateKey;
use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Curves\NistCurve;

/**
 * Helper class used for conversion between binary data representing keys
 * and keys represented as objects.
 *
 * @package Lime\ExpressStatement\Crypto
 */
class KeyConverter {

    /** Public key component length */
    private const KEY_LENGTH = 32;

    /** Public key X coordinate offset */
    private const KEY_OFFSET_X = 1;

    /** Public key Y coordinate offset */
    private const KEY_OFFSET_Y = 33;

    /** Public key raw data length */
    private const KEY_DATA_LEN = 65;

    /**
     * Convert provided bytes (raw data) to public key.
     *
     * @param $publicKeyBytes string Public key bytes. These are 65 bytes, where the first byte indicates
     * encoding type and then there are 2x 32 bytes for public key X,Y points.
     * @return \Mdanter\Ecc\Crypto\Key\PublicKey Decoded public key.
     */
    function bytesToPublicKey(string $publicKeyBytes): PublicKey {
        // Validate data
        if (strlen($publicKeyBytes) != KeyConverter::KEY_DATA_LEN) {
            throw new \RuntimeException('EC public key data size invalid - key data must be 65 bytes long.');
        }

        // Extract X and Y points from encoded representation
        $xBytes = substr($publicKeyBytes, KeyConverter::KEY_OFFSET_X, KeyConverter::KEY_LENGTH);
        $yBytes = substr($publicKeyBytes, KeyConverter::KEY_OFFSET_Y, KeyConverter::KEY_LENGTH);

        // Convert bytes into large numbers
        $x = gmp_import($xBytes);
        $y = gmp_import($yBytes);

        // Prepare generator
        $generator = CurveFactory::getGeneratorByName(NistCurve::NAME_P256);

        // Generate and return public key
        return $generator->getPublicKeyFrom($x, $y);
    }

    /**
     * Convert private key bytes to private key.
     *
     * @param $privateKeyBytes string Private key bytes. These are 32 bytes representing a key value.
     * @return \Mdanter\Ecc\Crypto\Key\PrivateKey Decoded private key.
     */
    function bytesToPrivateKey(string $privateKeyBytes): PrivateKey {
        $generator = CurveFactory::getGeneratorByName(NistCurve::NAME_P256);
        $secretMultiplier = gmp_import($privateKeyBytes, 1, GMP_MSW_FIRST | GMP_BIG_ENDIAN);
        return $generator->getPrivateKeyFrom($secretMultiplier);
    }

}