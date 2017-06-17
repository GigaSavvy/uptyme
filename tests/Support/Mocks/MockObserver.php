<?php

namespace Tests\Support\Mocks;

use Gigasavvy\Uptime\Observer\Message;
use Gigasavvy\Uptime\Observer\Observer;

class MockObserver implements Observer
{
    protected $messages = [];

    public function update(Message $message)
    {
        $this->messages[] = $message;
    }

    public function messages()
    {
        return $this->messages;
    }
}
