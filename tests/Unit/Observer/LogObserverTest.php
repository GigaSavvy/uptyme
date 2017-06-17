<?php

namespace Tests\Unit\Observer;

use Gigasavvy\Uptime\Observer\LogObserver;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Tests\Support\Mocks\MockObservable;
use Tests\TestCase;

class LogObserverTest extends TestCase
{
    /** @test */
    public function itLogsMessages()
    {
        $observable = new MockObservable();

        $logger = new Logger('test_logger');
        $handler = new TestHandler();
        $logger->pushHandler($handler);

        $observable->attach(new LogObserver($logger));

        $observable->notify('This should be logged.');

        $this->assertCount(1, $handler->getRecords());
        $this->assertTrue($handler->hasRecord(
            'This should be logged.',
            Logger::DEBUG
        ));
    }

    /** @test */
    public function itLogsAtGivenLevel()
    {
        $observable = new MockObservable();

        $logger = new Logger('test_logger');
        $handler = new TestHandler();
        $logger->pushHandler($handler);

        $observable->attach(new LogObserver($logger, Logger::CRITICAL));

        $observable->notify('This should be logged critically.');

        $this->assertTrue($handler->hasRecord(
            'This should be logged critically.',
            Logger::CRITICAL
        ));
    }
}
