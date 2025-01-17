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

namespace Jorijn\Bitcoin\Dca\Service\Kraken;

use Jorijn\Bitcoin\Dca\Client\KrakenClientInterface;
use Jorijn\Bitcoin\Dca\Service\BalanceServiceInterface;

class KrakenBalanceService implements BalanceServiceInterface
{
    protected KrakenClientInterface $client;

    public function __construct(KrakenClientInterface $client)
    {
        $this->client = $client;
    }

    public function supportsExchange(string $exchange): bool
    {
        return 'kraken' === $exchange;
    }

    public function getBalances(): array
    {
        $response = $this->client->queryPrivate('Balance');
        $rows = [];

        foreach ($response as $symbol => $available) {
            $rows[] = [$symbol, $available.' '.$symbol, $available.' '.$symbol];
        }

        return $rows;
    }
}
