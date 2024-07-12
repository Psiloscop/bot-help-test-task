<?php

namespace App\Message\Command;

readonly class AccountEventProcessCommand
{
    public function __construct(
        private int $accountId,
        private int $eventId,
    )
    {}

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }
}