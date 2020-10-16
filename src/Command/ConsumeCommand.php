<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ConsumeService;
use Predis\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class ConsumeCommand extends Command
{
    private Client $redis;
    private ConsumeService $consumeService;

    public function __construct(
        Client $redis,
        ConsumeService $consumeService,
        string $name = null
    ) {
        parent::__construct($name);
        $this->redis = $redis;
        $this->consumeService = $consumeService;
    }

    protected function configure(): void
    {
        $this
            ->setName('consume-data')
            ->setDescription('Consume accounts data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (($accountId = $this->redis->lpop('queue')) !== null) {
            while (($data = $this->redis->lpop('queue:' . $accountId)) !== null) {
                $event = json_decode($data, true);

                try {
                    $this->consumeService->processEvent($event);

                    $output->writeln('Account id: ' . $event['accountId'] . ', eventId: ' . $event['eventId']);
                } catch (Throwable $e) {
                    $this->redis->lpush('queue', [$accountId]);
                    $this->redis->lpush('queue:' . $accountId, $event);
                }
            }
        }

        return 0;
    }
}
