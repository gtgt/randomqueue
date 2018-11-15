<?php
namespace RandomQueue\Controller;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use RandomQueue\Job\RandomNumberJob;
use RandomQueue\Queue\Worker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobController extends AbstractController implements LoggerAwareInterface {

    use LoggerAwareTrait;

    protected const CONTROLLER_ACTIONS = [
        '/job/new' => 'new',
    ];

    /**
     * @var Worker
     */
    protected $worker;

    public function __construct(Worker $worker) {
        $this->worker = $worker;
    }

    public function newAction(Request $request) {
        $theNumber = $request->request->getInt('number');
        $this->worker->addJob(new RandomNumberJob($theNumber));
        $this->logger->info(sprintf('Added job by http with number: %d.', $theNumber));
        return new Response('OK');
    }
}
