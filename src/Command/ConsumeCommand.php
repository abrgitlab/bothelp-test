<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ConsumeService;
use Predis\Client;
use Superbalist\PubSub\Redis\RedisPubSubAdapter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use Wrep\Daemonizable\Command\EndlessCommand;

class ConsumeCommand extends EndlessCommand
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

    protected function starting(InputInterface $input, OutputInterface $output): void
    {
        $adapter = new RedisPubSubAdapter($this->redis);
        $this->processData();
        $adapter->subscribe('queue', function () {
            $this->processData();
        });
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }

    private function processData(): void
    {
        while (($data = $this->redis->lpop('queue')) !== null) {
            $event = json_decode($data, true);

            try {
                $this->consumeService->processEvent($event);
            } catch (Throwable $e) {
                $this->redis->lpush('queue', $event);
            }
        }
    }
}
