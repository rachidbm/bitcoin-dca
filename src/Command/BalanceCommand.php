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

namespace Jorijn\Bitcoin\Dca\Command;

use Jorijn\Bitcoin\Dca\Service\BalanceService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class BalanceCommand extends Command
{
    protected BalanceService $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        parent::__construct(null);
        $this->balanceService = $balanceService;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Gets the balance from the exchange and tests the API key')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $rows = $this->balanceService->getBalances();

            $table = new Table($output);
            $table->setHeaders(['Currency', 'Balance', 'Available']);
            $table->setRows($rows);
            $table->render();

            $io->success('Success!');
        } catch (Throwable $exception) {
            $io->error($exception->getMessage());

            return 1;
        }

        return 0;
    }
}
