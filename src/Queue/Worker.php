<?php


namespace RandomQueue\Queue;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;
use RandomQueue\Event\JobFailedEvent;
use RandomQueue\Event\JobSuccessfulEvent;
use RandomQueue\Exception\JobFailedException;
use RandomQueue\Job\JobInterface;
use RandomQueue\RandomQueueEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Worker {

    /**
     * @var AbstractConnection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $channelName;

    /**
     * @var JobToMessageTransformer
     */
    protected $transformer;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Worker constructor.
     *
     * @param AbstractConnection       $connection
     * @param string                   $channelName
     * @param JobToMessageTransformer  $transformer
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(AbstractConnection $connection, string $channelName, JobToMessageTransformer $transformer, EventDispatcherInterface $eventDispatcher) {
        $this->connection = $connection;
        $this->channelName = $channelName;
        $this->transformer = $transformer;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Returns RabbitMQ channel. Also connects on first call.
     *
     * @return AMQPChannel
     */
    protected function getChannel(): AMQPChannel {
        return $this->connection->channel();
    }

    /**
     * @param JobInterface $job
     */
    public function addJob(JobInterface $job): void {
        $channel = $this->getChannel();
        $channel->queue_declare($this->channelName, FALSE, TRUE, FALSE, FALSE);
        $channel->basic_publish($this->transformer->transform($job), '', $this->channelName);
    }

    /**
     * @throws \ErrorException
     */
    public function consume(): void {
        $channel = $this->getChannel();
        $channel->queue_declare($this->channelName, FALSE, TRUE, FALSE, FALSE);
        $channel->basic_qos(NULL, 1, NULL);
        $channel->basic_consume($this->channelName, '', FALSE, FALSE, FALSE, FALSE, function(AMQPMessage $message) {
            $job = $this->transformer->reverseTransform($message);
            $e = NULL;
            try {
                $result = $job->doIt();
            } catch (\Exception $e) {
                // always use JobFailedException
                if (!$e instanceof JobFailedException) {
                    $e = new JobFailedException(sprintf('Job (%s) failed: %d!', \get_class($job), $e->getMessage()), 0, $e);
                }
                $this->eventDispatcher->dispatch(RandomQueueEvents::JOB_FAILED, new JobFailedEvent($job, $message, $e));
            }
            if ($e === NULL) {
                $this->eventDispatcher->dispatch(RandomQueueEvents::JOB_SUCCESSFUL, new JobSuccessfulEvent($job, $message, $result));
            }
            $channel = $message->delivery_info['channel'];
            // acknowledge if supported
            if ($channel instanceof AMQPChannel) {
                $channel->basic_ack($message->delivery_info['delivery_tag']);
            }
        });

        while (\count($channel->callbacks)) {
            $channel->wait();
        }

        $this->close();
    }

    /**
     * Closes channel and connection.
     * Can be reopened by calling cvonsume() or addJob().
     */
    public function close(): void {
        $this->getChannel()->close();
        $this->connection->close();
    }

    public function __destruct() {
        $this->close();
    }
}
