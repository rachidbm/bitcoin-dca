<?php

declare(strict_types=1);

/*
 * This file is part of the Bitcoin-DCA package.
 *
 * (c) Jorijn Schrijvershof <jorijn@jorijn.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jorijn\Bitcoin\Dca\Component;

use BitWasp\Bitcoin\Address\AddressCreator;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Key\Deterministic\HdPrefix\GlobalPrefixConfig;
use BitWasp\Bitcoin\Key\Deterministic\HdPrefix\NetworkConfig;
use BitWasp\Bitcoin\Key\Deterministic\Slip132\Slip132;
use BitWasp\Bitcoin\Key\KeyToScript\KeyToScriptHelper;
use BitWasp\Bitcoin\Network\NetworkFactory;
use BitWasp\Bitcoin\Network\Slip132\BitcoinRegistry;
use BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\Base58ExtendedKeySerializer;
use BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\ExtendedKeySerializer;
use Jorijn\Bitcoin\Dca\Exception\NoMasterPublicKeyAvailableException;

class AddressFromMasterPublicKeyComponent implements AddressFromMasterPublicKeyComponentInterface
{
    public function derive(string $masterPublicKey, $path = '0/0'): string
    {
        if (empty($masterPublicKey)) {
            throw new \InvalidArgumentException('Master Public Key cannot be empty');
        }

        $adapter = Bitcoin::getEcAdapter();
        $network = NetworkFactory::bitcoin();
        $slip132 = new Slip132(new KeyToScriptHelper($adapter));
        $bitcoin_prefixes = new BitcoinRegistry();

        switch ($masterPublicKey[0] ?? null) {
            case 'x':
                $pubPrefix = $slip132->p2pkh($bitcoin_prefixes);
                $pub = $masterPublicKey;

                break;

            case 'y':
                $pubPrefix = $slip132->p2shP2wpkh($bitcoin_prefixes);
                $pub = $masterPublicKey;

                break;

            case 'z':
                $pubPrefix = $slip132->p2wpkh($bitcoin_prefixes);
                $pub = $masterPublicKey;

                break;

            default:
                throw new NoMasterPublicKeyAvailableException('no master public key available');

                break;
        }

        $serializer = new Base58ExtendedKeySerializer(
            new ExtendedKeySerializer($adapter, new GlobalPrefixConfig([
                new NetworkConfig($network, [
                    $pubPrefix,
                ]),
            ]))
        );

        $key = $serializer->parse($network, $pub);
        $child_key = $key->derivePath($path);

        return $child_key->getAddress(new AddressCreator())->getAddress();
    }

    public function supported(): bool
    {
        // this component only works on PHP 64-bits
        return \PHP_INT_SIZE === 8;
    }
}
