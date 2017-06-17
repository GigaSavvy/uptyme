<?php

namespace Gigasavvy\HttpsChecker\Observer;

use Monolog\Logger;

class LogObserver implements Observer
{
    /**
     * The logger instance.
     *
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * The PSR level to log at.
     *
     * @var int
     */
    protected $level;

    /**
     * Create a new instance.
     *
     * @param  \Monolog\Logger  $logger
     * @param  int  $level
     */
    public function __construct(Logger $logger, $level = Logger::DEBUG)
    {
        $this->logger = $logger;
        $this->level = $level;
    }

    /**
     * {@inheritDoc}
     */
    public function update(Message $message)
    {
        $this->logger->addRecord($this->level, $message, $message->data());
    }
}
