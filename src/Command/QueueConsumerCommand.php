<?php
namespace RandomQueue\Command;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use RandomQueue\Queue\Worker;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueueConsumerCommand extends Command implements LoggerAwareInterface {

    use LoggerAwareTrait;

    /**
     * @var Worker
     */
    protected $worker;

    public function __construct(string $name = NULL, Worker $worker = NULL) {
        parent::__construct($name);
        if (NULL === $worker) {
            throw new InvalidConfigurationException(sprintf('No %s defined!', Worker::class));
        }
        $this->worker = $worker;
    }

    protected function configure() {
        $this->setDescription('Start a worker to process those magic numbers!');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->logger->info('Consumer started!');
        $this->worker->consume();
    }
}
