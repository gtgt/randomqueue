<?php
namespace RandomQueue;

use RandomQueue\DependencyInjection\Compiler\ControllerServicePass;

class RandomQueueHttpContainerBuilder extends AbstractRandomQueueContainerBuilder {
    /**
     * Compiles controller services
     *
     * @throws \Exception
     */
    public function __construct() {
        $this->addCompilerPass(new ControllerServicePass());
        parent::__construct();
    }
}
