<?php

namespace Gigasavvy\HttpsChecker\Observer;

interface Observer
{
    /**
     * Update the observer.
     *
     * @param  \Gigasavvy\HttpsChecker\Observer\Message  $message
     * @return void
     */
    public function update(Message $message);
}
