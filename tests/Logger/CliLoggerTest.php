<?php
namespace Language\Tests\Logger;

use Language\Logger\CliLogger;

class CliLoggerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $mockLogger;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    protected function setUp()
    {
        $this->mockLogger = $this->createMock(CliLogger::class);
        $this->logger = new CliLogger;
    }

    /**
     * @dataProvider \Language\Tests\Helpers\LoggerHelper::logLevelProvider
     */
    public function testExecuteLog($level)
    {
        $this->mockLogger
            ->expects($this->once())
            ->method($level)
            ->with($level);

        $this->mockLogger->{$level}($level);
    }

    /**
     * @dataProvider \Language\Tests\Helpers\LoggerHelper::logLevelProvider
     */
    public function testResultLog($level)
    {
        $this->expectOutputString(date('Y-m-d H:i:s') . " [$level] $level");
        $this->logger->{$level}($level);
    }
}