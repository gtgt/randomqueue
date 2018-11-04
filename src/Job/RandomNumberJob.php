<?php


namespace RandomQueue\Job;


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
    public function __construct(int $theNumber) {
        $this->theNumber = $theNumber;
    }

    /**
     * {@inheritDoc}
     */
    public function doIt() {
        // 66% of jobs should fail. Reminder of division by 3 should do it :)
        if ($this->theNumber % 3) {
            throw new JobFailedException('Wrong number!');
        }
        return 42;
    }
}
