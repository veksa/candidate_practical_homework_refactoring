<?php
namespace Language\Tests\Helpers;

class LoggerHelper
{
    protected function getLogLevels()
    {
        $rc = new \ReflectionClass('\Psr\Log\LogLevel');
        $levels = $rc->getConstants();

        return $levels;
    }

    public function logLevelProvider()
    {
        $levels = $this->getLogLevels();
        array_walk($levels, function (&$level) {
            $level = [$level];
        });

        return $levels;
    }
}