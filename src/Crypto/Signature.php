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
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Crypto\Signature\SignHasher;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

/**
 * Class that simplifies generating and validating ECDSA signatures.
 *
 * @package Lime\ExpressStatement\Crypto
 */
class Signature {

    /**
     * Compute ECDSA signature of provided data using a given private key.
     *
     * @param $data string Data to be signed.
     * @param $privateKey PrivateKey Private key used for signature.
     *
     * @return string Signature bytes.
     *
     * @throws \RuntimeException
     */
    public function computeDataSignature(string $data, PrivateKey $privateKey): string {
        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getNistCurves()->generator256();
        $algorithm = 'sha256';

        $random = RandomGeneratorFactory::getRandomGenerator();
        $randomK = $random->generate($generator->getOrder());

        $signer = new Signer($adapter);
        $hasher = new SignHasher($algorithm);
        $hash = $hasher->makeHash($data, $generator);

        $signature = $signer->sign($privateKey, $hash, $randomK);

        $serializer = new DerSignatureSerializer();
        return  $serializer->serialize($signature);
    }

    /**
     * Validate provided ECDSA signature for given data using a provided public key.
     *
     * @param $data string Data to be used for signature validation.
     * @param $signature string Signature data.
     * @param $publicKey PublicKey Public key for data verification.
     *
     * @return bool True if the signature is correct, false otherwise.
     *
     * @throws \FG\ASN1\Exception\ParserException
     * @throws \RuntimeException
     */
    public function validateDataSignature(string $data, string $signature, PublicKey $publicKey): bool {
        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getNistCurves()->generator384();
        $algorithm = 'sha256';

        $sigSerializer = new DerSignatureSerializer();
        $sig = $sigSerializer->parse($signature);

        $signer = new Signer($adapter);
        $hasher = new SignHasher($algorithm);
        $hash = $hasher->makeHash($data, $generator);
        return $signer->verify($publicKey, $sig, $hash);
    }

    /**
     * Generate random cryptographic nonce and encode it as Base64.
     *
     * @return string Base64 encoded cryptographic nonce.
     *
     * @throws \Exception
     */
    public function nonce(): string {
        return base64_encode(random_bytes(16));
    }

}