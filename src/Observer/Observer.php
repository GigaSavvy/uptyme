<?php

namespace Gigasavvy\Uptime\Observer;

interface Observer
{
    /**
     * Update the observer.
     *
     * @param  \Gigasavvy\Uptime\Observer\Message  $message
     * @return void
     */
    public function update(Message $message);
}
