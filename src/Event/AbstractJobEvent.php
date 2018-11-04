<?php


namespace RandomQueue\Event;


use PhpAmqpLib\Message\AMQPMessage;
use RandomQueue\Job\JobInterface;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractJobEvent extends Event {
    /**
     * @var JobInterface
     */
    protected $job;
    /**
     * @var AMQPMessage
     */
    protected $message;

    /**
     * AbstractJobEvent constructor.
     *
     * @param JobInterface $job
     * @param AMQPMessage  $message
     */
    public function __construct(JobInterface $job, AMQPMessage $message) {
        $this->job = $job;
        $this->message = $message;
    }

    /**
     * @return JobInterface
     */
    public function getJob(): JobInterface {
        return $this->job;
    }

    /**
     * @return AMQPMessage
     */
    public function getMessage(): AMQPMessage {
        return $this->message;
    }
}
