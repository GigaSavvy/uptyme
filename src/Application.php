<?php

namespace Gigasavvy\Uptime;

use Gigasavvy\Uptime\Commands\RunCommand;
use Gigasavvy\Uptime\UptimeChecker;
use GuzzleHttp\Client as HttpClient;
use Monolog\Logger;
use Symfony\Component\Console\Application as Console;

class Application
{
    protected $console;

    public function create()
    {
        $this->console = new Console();

        $checker = new UptimeChecker(new HttpClient());

        $this->console->add(new RunCommand($checker));
    }

    public function run()
    {
        $this->console->run();
    }
}
