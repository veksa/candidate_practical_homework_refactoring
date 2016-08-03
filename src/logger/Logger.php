<?php
namespace Language\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Class Logger
 *
 * @package Language\Logger
 */
abstract class Logger extends AbstractLogger
{
    /**
     * Returns the text display of the specified level.
     *
     * @param integer $level the message level.
     *
     * @return string the text display of the level
     */
    protected function getLevelName($level)
    {
        static $levels = [
            LogLevel::ALERT => 'alert',
            LogLevel::CRITICAL => 'critical',
            LogLevel::DEBUG => 'debug',
            LogLevel::EMERGENCY => 'emergency',
            LogLevel::ERROR => 'error',
            LogLevel::INFO => 'info',
            LogLevel::NOTICE => 'notice',
            LogLevel::WARNING => 'warning'
        ];

        return isset($levels[$level]) ? $levels[$level] : 'unknown';
    }

    /**
     * Format messages to log
     *
     * @param string $level
     * @param string $message
     *
     * @return string
     */
    protected function formatMessage($level, $message)
    {
        $level = $this->getLevelName($level);

        return date('Y-m-d H:i:s') . " [$level] $message";
    }
}