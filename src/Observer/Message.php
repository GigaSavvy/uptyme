<?php

namespace Gigasavvy\Uptime\Observer;

class Message
{
    /**
     * The message's text.
     *
     * @var string
     */
    protected $text;

    /**
     * Additional data to support the message.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new instance.
     *
     * @param  string  $text
     * @param  array  $data
     */
    public function __construct($text, array $data = [])
    {
        $this->text = $text;
        $this->data = $data;
    }

    /**
     * Get the text.
     *
     * @return string
     */
    public function text()
    {
        return $this->text;
    }

    /**
     * Get the data.
     *
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Convert the message to a string.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->text();
    }
}
