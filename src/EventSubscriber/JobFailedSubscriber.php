<?php


namespace RandomQueue\EventSubscriber;


use Psr\Log\LoggerInterface;
use RandomQueue\Event\JobFailedEvent;
use RandomQueue\Queue\Worker;
use RandomQueue\RandomQueueEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JobFailedSubscriber implements EventSubscriberInterface {

    /**
     * @var Worker
     */
    protected $worker;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Swift mailer
     *
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * E-mail address to notify after max fails
     *
     * @var string
     */
    protected $notifyEmail;

    /**
     * JobFailedSubscriber constructor.
     *
     * @param Worker          $worker
     * @param LoggerInterface $logger
     * @param \Swift_Mailer   $mailer
     * @param string          $notifyEmail
     */
    public function __construct(Worker $worker, LoggerInterface $logger, \Swift_Mailer $mailer, string $notifyEmail) {
        $this->worker = $worker;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->notifyEmail = $notifyEmail;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents() {
        // return the subscribed events, their methods and priorities
        return [
            RandomQueueEvents::JOB_FAILED => [
                ['processException', 10],
                ['logException', 0],
                ['notifyException', -10],
            ]
        ];
    }

    /**
     * @param JobFailedEvent $event
     */
    public function processException(JobFailedEvent $event): void {
        $job = $event->getJob();
        $job->incFailCount();
        if ($job->getRetriesLeft() > 0) {
            $this->worker->addJob($job);
        }
    }

    /**
     * @param JobFailedEvent $event
     */
    public function logException(JobFailedEvent $event): void {
        $jobFailedException = $event->getException();
        $this->logger->warning(sprintf('Try %d: %s', $event->getJob()->getFailCount(), $jobFailedException->getMessage()), ['exception' => $jobFailedException]);
    }

    /**
     * @param JobFailedEvent $event
     */
    public function notifyException(JobFailedEvent $event): void {
        $job = $event->getJob();
        if ($job->getRetriesLeft() <= 0) {
            $deliveryTag = $event->getMessage()->delivery_info['delivery_tag'];
            try {
                $this->mailer->send((new \Swift_Message('Job failed :('))
                    ->setFrom([$this->notifyEmail])
                    ->setTo([$this->notifyEmail])
                    ->setBody(sprintf('Job failed %d times!'.PHP_EOL.'d.tag: %d', $job->getFailCount(), $deliveryTag))
                );
            } catch (\Swift_TransportException $e) {
                $this->logger->error(sprintf('Mailer error: %s', $e->getMessage()), ['exception' => $e]);
            }
        }
    }
}
