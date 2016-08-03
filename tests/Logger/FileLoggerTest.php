<?php
namespace Language\Tests\Logger;

use Language\Logger\FileLogger;

class FileLoggerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    protected function setUp()
    {
        $this->logger = $this->createMock(FileLogger::class);
    }

    /**
     * @dataProvider \Language\Tests\Helpers\LoggerHelper::logLevelProvider
     */
    public function testLog($level)
    {
        $this->logger
            ->expects($this->once())
            ->method($level)
            ->with($level);

        $this->logger->{$level}($level);
    }
}