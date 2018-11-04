<?php


namespace RandomQueue\Exception;


use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

class InvalidMessageException extends \DomainException {

    /**
     * @var AMQPMessage
     */
    protected $amqpMessage;

    /**
     * InvalidMessageException constructor.
     *
     * @param AMQPMessage    $amqpMessage
     * @param string         $message
     * @param int            $code
     * @param Throwable|NULL $previous
     */
    public function __construct(AMQPMessage $amqpMessage, string $message = "", int $code = 0, Throwable $previous = NULL) {
        parent::__construct('Invalid AMQP message: '.$message, $code, $previous);
        $this->amqpMessage = $amqpMessage;
    }

    /**
     * @return AMQPMessage
     */
    public function getAmqpMessage(): AMQPMessage {
        return $this->amqpMessage;
    }
}
