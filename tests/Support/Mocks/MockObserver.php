<?php

namespace Tests\Support\Mocks;

use Gigasavvy\HttpsChecker\Observer\Message;
use Gigasavvy\HttpsChecker\Observer\Observer;

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
