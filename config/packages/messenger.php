<?php

use App\Message\Command\AccountEventProcessCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $workerCount = $_ENV['MESSENGER_WORKER_COUNT'];

    $queues = [];
    for ( $queueNumber = 0; $queueNumber < $workerCount; $queueNumber++ )
    {
        $queues["bot_help.account_event_process.command.queue$queueNumber"] = [
            'binding_keys' => [
                "queue$queueNumber"
            ]
        ];
    }

    $containerConfigurator->extension('framework', [
        'messenger' => [
            'transports' => [
                'async_process_account_event' => [
                    'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                    'options' => [
                        'exchange' => [
                            'name' => 'bot_help.account_event_process.command',
                            'type' => 'direct',
                        ],
                        'queues' => $queues,
                    ],
                ],
            ],
            'routing' => [
                AccountEventProcessCommand::class => 'async_process_account_event',
            ],
        ],
    ]);
};
