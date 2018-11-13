<?php
namespace RandomQueue;

use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;

class RandomQueueCommandContainerBuilder extends AbstractRandomQueueContainerBuilder {

    /**
     * Compiles console commands
     *
     * @throws \Exception
     */
    public function __construct() {
        $this->addCompilerPass(new AddConsoleCommandPass());
        parent::__construct();
    }
}
