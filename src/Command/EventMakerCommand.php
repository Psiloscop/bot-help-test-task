<?php

namespace App\Command;

use App\Service\AccountEventProcessDispatcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

#[AsCommand(
    name: 'app:event-maker',
    description: 'Making events and dispatching them.',
)]
class EventMakerCommand extends Command
{
    public function __construct(
        private AccountEventProcessDispatcher $accountEventProcessDispatcher
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('event-amount', null, InputOption::VALUE_REQUIRED, 'Event amount to generate.')
            ->addOption('max-account-id', null, InputOption::VALUE_REQUIRED, 'Define max account id. Min is 0')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $maxAccountId = $input->getOption('max-account-id');
        if ( $maxAccountId !== null && $maxAccountId <= 0  )
        {
            $io->error(sprintf('Wrong max account ID: %s. It must be positive non-zero value.', $maxAccountId));

            return Command::FAILURE;
        }
        else
        {
            $maxAccountId = 1000;
        }

        $eventAmount = $input->getOption('event-amount');
        if ( !$eventAmount )
        {
            $eventAmount = 10000;
        }

        try
        {
            for ( $eventNumber = 1; $eventNumber <= $eventAmount; $eventNumber++ )
            {
                $accountId = rand(1, $maxAccountId);
                $eventId = rand(1, 100);

                $this->accountEventProcessDispatcher->dispatchCommand($accountId, $eventId);
            }
        }
        catch ( ExceptionInterface )
        {
            $io->error(sprintf('Error occurred while dispatching message #%u out of %u.', $eventNumber, $eventAmount));

            return Command::FAILURE;
        }

        $io->success(sprintf('All %u events have been dispatched.', $eventAmount));

        return Command::SUCCESS;
    }
}
