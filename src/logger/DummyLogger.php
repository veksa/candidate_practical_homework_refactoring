<?php
namespace Language\Logger;

/**
 * Class DummyLogger
 *
 * @package Language\Logger
 */
class DummyLogger extends Logger
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
    }
}