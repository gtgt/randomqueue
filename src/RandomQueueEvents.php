<?php


namespace RandomQueue;


class RandomQueueEvents {
    /**
     * Fires when a job successfully done
     */
    public const JOB_SUCCESSFUL = 'randomqueue.job.successful';
    /**
     * Fires when a job fails
     */
    public const JOB_FAILED = 'randomqueue.job.failed';
}
