<?php
namespace Language\Tests\Logger;

use Language\Logger\DummyLogger;

class DummyLoggerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $mockLogger;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    protected function setUp()
    {
        $this->mockLogger = $this->createMock(DummyLogger::class);
        $this->logger = new DummyLogger;
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
}