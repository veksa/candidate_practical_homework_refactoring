<?php
namespace Language\Logger;

use Language\Exceptions\InvalidArgumentException;

/**
 * Class FileLogger
 * 
 * @package Language\Logger
 */
class FileLogger extends Logger
{
    /**
     * @var null|string
     */
    private $logFile = null;

    /**
     * @var null|resource
     */
    private $fp = null;

    /**
     * FileLogger constructor.
     * 
     * @param null|string $logFile
     */
    public function __construct($logFile = null)
    {
        $this->logFile = $logFile;
        if ($this->logFile === null) {
            $this->logFile = '/build/logs/debug.log';
        }

        $logPath = dirname($this->logFile);
        if (!is_dir($logPath)) {
            mkdir($logPath, 0775, true);
        }

        if (($this->fp = @fopen($this->logFile, 'a')) === false) {
            throw new InvalidArgumentException('Unable to append to log file: ' . $this->logFile);
        }
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
        $message = $this->formatMessage($level, $message);

        @flock($this->fp, LOCK_EX);
        @file_put_contents($this->logFile, $message, FILE_APPEND | LOCK_EX);
        @flock($this->fp, LOCK_UN);
    }

    /**
     * FileLogger destructor.
     */
    public function __destruct()
    {
        @fclose($this->fp);
    }
}