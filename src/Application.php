<?php

namespace Gigasavvy\HttpsChecker;

use Gigasavvy\HttpsChecker\Commands\RunCommand;
use Gigasavvy\HttpsChecker\HttpsChecker;
use GuzzleHttp\Client as HttpClient;
use Monolog\Logger;
use Symfony\Component\Console\Application as Console;

class Application
{
    protected $console;

    public function create()
    {
        $this->console = new Console();

        $checker = new HttpsChecker(new HttpClient());

        $this->console->add(new RunCommand($checker));
    }

    public function run()
    {
        $this->console->run();
    }
}
