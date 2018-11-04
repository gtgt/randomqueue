<?php
namespace RandomQueue\Command;

use PhpAmqpLib\Connection\AbstractConnection;
use RandomQueue\Job\RandomNumberJob;
use RandomQueue\Queue\Worker;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueueJobAddCommand extends Command {

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
        $this->setDescription('Add a magic number to queue to process!')
            ->addArgument('number', InputArgument::REQUIRED, 'The magic number.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->worker->addJob(new RandomNumberJob($input->getArgument('number')));
        $this->worker->close();
    }
}
