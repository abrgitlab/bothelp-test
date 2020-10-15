<?php

declare(strict_types=1);

namespace App\Util;

use Generator;
use Iterator;

class BatchGenerator
{
    const MESSAGES_COUNT = 10000;
    const ACCOUNTS_COUNT = 1000;
    const MAX_MESSAGES_PER_ACCOUNT = 10;
    const MAX_MESSAGES_PER_BATCH = 10;

    private array $batch = [];
    private int $batchSize;

    public function getBatch(): Generator
    {
        $this->resetBatch();
        foreach ($this->generateBatch() as $generateBatch) {
            yield $generateBatch;

            $this->resetBatch();
        }
    }

    private function generateBatch(): Iterator
    {
        $availableAccounts = array_fill(0, self::ACCOUNTS_COUNT, 0);

        $eventId = 0;
        while (count($availableAccounts) > 0 && ($eventId++) < self::MESSAGES_COUNT) {
            $accountId = array_keys($availableAccounts)[random_int(0, count($availableAccounts) - 1)];
            $eventForAccountId = ++$availableAccounts[$accountId];

            $this->batch[] = [
                'accountId' => $accountId,
                'eventId' => $eventForAccountId,
                'eventName' => 'Event ' . $eventForAccountId,
            ];

            if (count($this->batch) >= $this->batchSize) {
                yield $this->batch;
            }

            if ($eventForAccountId > self::MAX_MESSAGES_PER_ACCOUNT) {
                unset($availableAccounts[$accountId]);
            }
        }
    }

    private function resetBatch(): void
    {
        $this->batchSize = random_int(1, self::MAX_MESSAGES_PER_BATCH);
        $this->batch = [];
    }
}