<?php

namespace Gigasavvy\Uptime\Observer;

abstract class Observable
{
    /**
     * The attached ovservers.
     *
     * @var array
     */
    private $observers = [];

    /**
     * Get the observers attached to this object.
     *
     * @return \Gigasavvy\Uptime\Observer\Observer[]
     */
    public function observers()
    {
        return $this->observers;
    }

    /**
     * Attach a new observer to the list of observers.
     *
     * @param  \Gigasavvy\Observer\Observer $observer
     * @return void
     */
    public function attach(Observer $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     * Notify the observers.
     *
     * @return void
     */
    public function notify($message, array $data = [])
    {
        foreach ($this->observers as $observer) {
            $observer->update(new Message($message, $data));
        }
    }

    /**
     * Detach an observer from the list of observers.
     *
     * @param  \Gigasavvy\Observer\Observer $observer
     * @return void
     */
    public function detach(Observer $observer)
    {
        if (($key = array_search($observer, $this->observers, true)) !== false) {
            unset($this->observers[$key]);
        }
    }
}
