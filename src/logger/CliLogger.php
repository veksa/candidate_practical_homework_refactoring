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
        $message = $this->formatMessage($level, $message);

        echo $message;
    }
}