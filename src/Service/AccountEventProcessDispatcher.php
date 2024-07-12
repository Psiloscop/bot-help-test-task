<?php

namespace App\Service;

use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\Command\AccountEventProcessCommand;

readonly class AccountEventProcessDispatcher
{
    public function __construct(
        private MessageBusInterface $messageBus,
        #[Autowire(param: 'divide_mod')]
        private int $divideMod
    )
    {}

    /**
     * @throws ExceptionInterface
     */
    public function dispatchCommand(int $accountId, int $eventId): void
    {
        $queueNum = $accountId % $this->divideMod;

        $command = new AccountEventProcessCommand($accountId, $eventId);

        $this->messageBus->dispatch(
            $command,
            [ new AmqpStamp(routingKey: "queue$queueNum") ]
        );
    }
}