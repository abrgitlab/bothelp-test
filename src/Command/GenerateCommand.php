<?php

declare(strict_types=1);

namespace App\Command;

use App\Util\BatchGenerator;
use Predis\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    private Client $redis;

    public function __construct(Client $redis, string $name = null)
    {
        parent::__construct($name);
        $this->redis = $redis;
    }

    protected function configure(): void
    {
        $this
            ->setName('generate-data')
            ->setDescription('Generate accounts data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ((new BatchGenerator($this->redis))->getBatch() as $batch) {
            foreach ($batch as $event) {
                $this->redis->rpush('queue', $event['accountId']);
                $this->redis->rpush('queue:' . $event['accountId'], [json_encode($event)]);
            }

            usleep(random_int(1, 1000));
        }

        return 0;
    }
}
