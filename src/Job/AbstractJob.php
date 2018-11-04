<?php


namespace RandomQueue\Job;

/**
 * Class AbstractJob
 *
 * @package RandomQueue\Job
 */
abstract class AbstractJob implements JobInterface {

    /**
     * @var int Stores fail count of the job.
     */
    protected $failCount = 0;

    /**
     * {@inheritDoc}
     */
    public function getFailCount(): int {
        return $this->failCount;
    }

    /**
     * {@inheritDoc}
     */
    public function incFailCount(): int {
        return ++$this->failCount;
    }

    public function getRetriesLeft(): int {
        return static::MAX_FAILS - $this->getFailCount();
    }

}
