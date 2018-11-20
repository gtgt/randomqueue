<?php


namespace RandomQueue\Test;


use PHPUnit\Framework\TestCase;

class AbstractTest extends TestCase {
    /**
     * @var array
     */
    private static $container = [];

    /**
     * @param bool $isDebug
     *
     * @return \ProjectServiceContainer|RandomQueueTestContainerBuilder
     * @throws \Exception
     */
    protected static function getContainer(bool $isDebug = true) {
        if (!array_key_exists($isDebug, self::$container)) {
            self::$container[$isDebug] = RandomQueueTestContainerBuilder::getContainer($isDebug);
        }
        return self::$container[$isDebug];
    }
}
