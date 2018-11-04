<?php


namespace RandomQueue\Queue;


use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use RandomQueue\Exception\InvalidMessageException;
use RandomQueue\Job\JobInterface;

/**
 * Class JobToMessageTransformer
 *
 * @package RandomQueue\Queue
 */
class JobToMessageTransformer {

    /**
     * @param JobInterface $job
     *
     * @return AMQPMessage
     */
    public function transform(JobInterface $job): AMQPMessage {
        return new AMQPMessage(serialize($job), [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'application_headers' => new AMQPTable([
                'x-class' => \get_class($job)
            ]),
        ]);
    }

    /**
     * @param AMQPMessage $message
     *
     * @return JobInterface
     */
    public function reverseTransform(AMQPMessage $message): JobInterface {
        $properties = $message->get_properties();
        if (!array_key_exists('application_headers', $properties)) {
            throw new InvalidMessageException($message, 'No application headers!');
        }

        /** @var AMQPTable $applicationHeaders7 */
        $applicationHeaders = $properties['application_headers'];
        $headers = $applicationHeaders->getNativeData();
        if (!array_key_exists('x-class', $headers) || !class_exists($headers['x-class']) || !is_a($headers['x-class'], JobInterface::class, TRUE)) {
            throw new InvalidMessageException(sprintf('Class not exists or not implements %s!', JobInterface::class));
        }
        return unserialize($message->getBody(), ['allowed_classes' => [$headers['x-class']]]);
    }

}
