<?php


namespace RandomQueue\Test;


use function array_key_exists;
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
        $index = (int)$isDebug;
        if (!array_key_exists($index, self::$container)) {
            self::$container[$index] = RandomQueueTestContainerBuilder::getContainer($index);
        }
        return self::$container[$index];
    }
}
