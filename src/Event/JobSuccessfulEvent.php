<?php


namespace RandomQueue\Event;


use PhpAmqpLib\Message\AMQPMessage;
use RandomQueue\Exception\JobFailedException;
use RandomQueue\Job\JobInterface;

class JobSuccessfulEvent extends AbstractJobEvent {
    protected $result;

    public function __construct(JobInterface $job, AMQPMessage $message, $result) {
        parent::__construct($job, $message);
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getResult() {
        return $this->result;
    }
}
