<?php
namespace Language\Logger;

use Language\Logger\Logger as AbstractLogger;

/**
 * Class CliLogger
 *
 * @package Language\Logger
 */
class CliLogger extends AbstractLogger
{
    private $setTimestamp;

    /**
     * CliLogger constructor.
     *
     * @param null|bool $logFile
     */
    public function __construct($setTimestamp = true)
    {
        $this->setTimestamp = $setTimestamp;
    }

    /**
     * Log implementation
     *
     * @param string $level
     * @param string $message
     *
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        if ($this->setTimestamp) {
            $message = $this->formatMessage($level, $message);
        }

        echo $message;
    }
}