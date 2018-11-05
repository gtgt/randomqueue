<?php


namespace RandomQueue\Log;

use Exception;
use Psr\Log\LoggerInterface;
use RandomQueue\Exception\LoggerException;

class DatabaseLogger implements LoggerInterface {
    public const DEBUG = 1; // Most Verbose
    public const INFO = 2; // ...
    public const NOTICE = 3; // ...
    public const WARN = 4; // ...
    public const ERROR = 5; // ...
    public const CRITICAL = 6; // ...
    public const ALERT = 7; // ...
    public const EMERGENCY = 8; // Least Verbose
    public const OFF = 9; // Nothing at all.

    protected const LOG_OPEN = 10;
    protected const OPEN_FAILED = 20;
    protected const LOG_CLOSED = 30;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int
     */
    protected $level;

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var \PDOStatement
     */
    private $stmt;


    /**
     * @param \PDO $pdo   Database storage
     * @param int  $level Priority
     *
     */
    public function __construct(\PDO $pdo, int $level) {
        $this->status = self::LOG_OPEN;
        try {
            // @TODO: move to services.yml
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_PERSISTENT, false);
        } catch (\PDOException $e) {
            $this->status = self::OPEN_FAILED;
        }
        $this->pdo = $pdo;
        $this->level = $level;
    }


    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     * @throws Exception
     */
    public function log($level, $message, array $context = []) {
        if ($this->level <= $level) {
            $message = (string)$message;
            $strContext = '';

            if ($context !== NULL && \count($context) > 0) {
                $strContext = json_encode($context);
            }

            $this->store($level, $message, $strContext);
        }
    }

    /**
     * @return bool|\PDOStatement
     */
    private function getStatement() {
        if (NULL === $this->stmt) {
            try {
                $this->stmt = $this->pdo->prepare('INSERT INTO log (time, level, message, context) VALUES(:time, :level, :message, :context)');
            } catch (\PDOException $e) {
                $this->status = self::LOG_CLOSED;
                throw new LoggerException(sprintf('Unable to prepare database log statement: %s', $e->errorInfo), $e->getCode(), $e);
            }
        }
        return $this->stmt;
    }

    /**
     * writeLine
     *
     * @param $level
     * @param $message
     * @param $strContext
     */
    protected function store($level, $message, $strContext) {
        if ($this->level === self::OFF) {
            return;
        }

        if ($this->status !== self::LOG_OPEN) {
            throw new LoggerException('Logger database connection is closed due to errors. Please refer to exception thrown before.');
        }

        if (($stmt = $this->getStatement()) instanceof \PDOStatement) {
            try {
                $stmt->execute([':time' => time(), ':level' => $level, ':message' => $message, ':context' => $strContext]);
            } catch (\PDOException $e) {
                $this->status = self::LOG_CLOSED;
                throw new LoggerException(sprintf('Unable to store log: %s', $e->errorInfo), $e->getCode(), $e);
            }
        }
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     * @throws Exception
     */
    public function emergency($message, array $context = []) {
        $this->log(self::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     *
     * @throws Exception
     */
    public function alert($message, array $context = []) {
        $this->log(self::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     *
     * @throws Exception
     */
    public function critical($message, array $context = []) {
        $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     *
     * @throws Exception
     */
    public function error($message, array $context = []) {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     *
     * @throws Exception
     */
    public function warning($message, array $context = []) {
        $this->log(self::WARN, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     *
     * @throws Exception
     */
    public function notice($message, array $context = []) {
        $this->log(self::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     *
     * @throws Exception
     */
    public function info($message, array $context = []) {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     *
     * @throws Exception
     */
    public function debug($message, array $context = []) {
        $this->log(self::DEBUG, $message, $context);
    }
}
