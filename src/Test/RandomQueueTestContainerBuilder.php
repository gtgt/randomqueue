<?php
namespace RandomQueue\Test;

use RandomQueue\AbstractRandomQueueContainerBuilder;
use RandomQueue\DependencyInjection\Compiler\ControllerServicePass;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;

class RandomQueueTestContainerBuilder extends AbstractRandomQueueContainerBuilder {

    /**
     * Compiles console and http passes
     *
     * @throws \Exception
     */
    public function __construct() {
        $this->addCompilerPass(new AddConsoleCommandPass());
        $this->addCompilerPass(new ControllerServicePass());
        parent::__construct();
    }
}
