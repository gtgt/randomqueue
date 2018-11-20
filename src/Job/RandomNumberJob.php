<?php


namespace RandomQueue\Job;


use RandomQueue\Exception\InvalidJobArgumentException;
use RandomQueue\Exception\JobFailedException;

/**
 * The answer of life by number!
 *
 * @package RandomQueue\Job
 */
class RandomNumberJob extends AbstractJob {

    /**
     * @var int
     */
    protected $theNumber;

    /**
     * RandomNumberJob constructor.
     *
     * @param int $theNumber
     */
    public function __construct($theNumber) {
        if (!is_numeric($theNumber)) {
            throw new InvalidJobArgumentException(sprintf('Invalid %s argument: %s.', static::class, $theNumber));
        }
        $this->theNumber = (int)$theNumber;
    }

    /**
     * {@inheritDoc}
     */
    public function doIt() {
        // 66% of jobs should fail. Reminder of division by 3 should do it :)
        $numberToTry = $this->getFailCount() + $this->theNumber;
        if ($numberToTry % 3) {
            throw new JobFailedException(sprintf('Wrong number: %d!', $numberToTry));
        }
        return 42;
    }
}
