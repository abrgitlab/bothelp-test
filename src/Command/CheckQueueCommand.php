<?php

namespace App\Command;

use Predis\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckQueueCommand extends Command
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
            ->setName('check-queue')
            ->setDescription('Check queue for emptiness');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queueSize = $this->redis->scard('queue');

        $output->writeln($queueSize);

        return 0;
    }
}
