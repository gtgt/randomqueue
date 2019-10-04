<?php
namespace RandomQueue\Event;

use PhpAmqpLib\Message\AMQPMessage;
use RandomQueue\Exception\JobFailedException;
use RandomQueue\Job\JobInterface;

class JobFailedEvent extends AbstractJobEvent {
    protected $exception;

    public function __construct(JobInterface $job, AMQPMessage $message, JobFailedException $e) {
        parent::__construct($job, $message);
        $this->exception = $e;
    }

    /**
     * @return JobFailedException
     */
    public function getException(): JobFailedException {
        return $this->exception;
    }
}
