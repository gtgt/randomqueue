<?php


namespace RandomQueue\Queue;


use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;
use RandomQueue\Event\JobFailedEvent;
use RandomQueue\Event\JobSuccessfulEvent;
use RandomQueue\Exception\JobFailedException;
use RandomQueue\Job\JobInterface;
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
     *
     * @return AMQPMessage
     */
    public function addJob(JobInterface $job): AMQPMessage {
        $channel = $this->getChannel();
        $channel->queue_declare($this->channelName, FALSE, TRUE, FALSE, FALSE);
        $message = $this->transformer->transform($job);
        $channel->basic_publish($message, '', $this->channelName);
        return $message;
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
            } catch (Exception $e) {
                // always use JobFailedException
                if (!$e instanceof JobFailedException) {
                    $e = new JobFailedException(sprintf('Job (%s) failed: %d!', \get_class($job), $e->getMessage()), 0, $e);
                }
                $this->eventDispatcher->dispatch(new JobFailedEvent($job, $message, $e));
            }
            if ($e === NULL) {
                $this->eventDispatcher->dispatch(new JobSuccessfulEvent($job, $message, $result));
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
        try {
            $this->getChannel()->close();
            $this->connection->close();
        } catch (Exception $e) {
            // doesn't matter if this fails
        }
    }

    public function __destruct() {
        $this->close();
    }
}
