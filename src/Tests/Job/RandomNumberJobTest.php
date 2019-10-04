<?php
namespace RandomQueue\Tests\Job;

use RandomQueue\Exception\JobFailedException;
use RandomQueue\Job\RandomNumberJob;
use RandomQueue\Test\AbstractTest;

class RandomNumberJobTest extends AbstractTest {
    /**
     * @expectedException \RandomQueue\Exception\InvalidJobArgumentException
     */
    public function testNonNumeric() {
       new RandomNumberJob('abc');
    }

    /**
     * Job should fail on 66% of numbers (new object created version)
     * and should return 42 when success
     */
    public function testFailRateAndReturnValueManyObjects(): void {
        $failed = 0;
        $runs = 1000;
        for ($i = 0; $i < $runs; $i++) {
            try {
                $return = (new RandomNumberJob($i))->doIt();
                $this->assertEquals(42, $return, 'Success job should return with 42');
            } catch (JobFailedException $e) {
                $failed++;
            } catch (\Exception $e) {
                $this->assertInstanceOf(JobFailedException::class, $e, sprintf('Job execution should drop %s only', JobFailedException::class));
            }
        }
        $this->assertEquals(66, (int)($failed / $runs * 100));
    }

    /**
     * Job should fail on 66% of runs (same object created version)
     * and should return 42 when success
     */
    public function testFailRateAndReturnValueOneObject(): void {
        $failed = 0;
        $runs = 1000;
        for ($i = 0; $i < $runs; $i++) {
            $return = NULL;
            $job = new RandomNumberJob($i);
            do {
                try {
                    $return = $job->doIt();
                } catch (JobFailedException $e) {
                    $job->incFailCount();
                    $i++;
                } catch (\Exception $e) {
                    $this->assertInstanceOf(JobFailedException::class, $e, sprintf('Job execution should drop %s only', JobFailedException::class));
                }
            } while (!$return || !$job->getRetriesLeft());
            $this->assertEquals(42, $return, 'Success job should return with 42');
            $failed += $job->getFailCount();
        }
        $this->assertEquals(66, (int)($failed / $runs * 100));
    }
}
