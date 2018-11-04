<?php


namespace RandomQueue\Job;

interface JobInterface {
    /**
     * Max allowed fails for the job - i.e., maximum number of retries.
     */
    public const MAX_FAILS = 3;

    /**
     * Get fail count
     *
     * @return int Current fail count
     */
    public function getFailCount(): int;

    /**
     * Increases fail count.
     *
     * @return int New fail count
     */
    public function incFailCount(): int;

    /**
     * How many retry left on this job?
     *
     * @return int If greater then 1, then job is engible for retry.
     */
    public function getRetriesLeft(): int;

    /**
     * @throws \Exception If any exception thrown, the job will fail.
     *
     * @return mixed The result of the job.
     */
    public function doIt();
}
