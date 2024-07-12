<?php

namespace App\Message\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Message\Command\AccountEventProcessCommand;

#[AsMessageHandler(
    fromTransport: 'async_process_account_event'
)]
class AccountEventProcessHandler
{
    public function __invoke(AccountEventProcessCommand $command): void
    {
        sleep(1);

//        file_put_contents('/app/process_log', "Account ID: {$command->getAccountId()}; Event ID: {$command->getEventId()}\n", FILE_APPEND);
    }
}