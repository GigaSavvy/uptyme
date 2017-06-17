<?php

namespace Gigasavvy\HttpsChecker\Commands;

use Gigasavvy\HttpsChecker\HttpsChecker;
use Gigasavvy\HttpsChecker\Observer\LogObserver;
use GuzzleHttp\Client as HttpClient;
use Monolog\Handler\SlackWebhookHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    private $checker;

    public function __construct(HttpsChecker $checker)
    {
        $this->checker = $checker;

        parent::__construct();
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('run');
        $this->setDescription('Run the HTTPS checker on the given domains.');
        $this->addArgument(
            'domains',
            InputArgument::REQUIRED|InputArgument::IS_ARRAY,
            'A comma separated list of domains to check.'
        );
        $this->addOption(
            'file',
            'f',
            InputOption::VALUE_NONE,
            'Whether the given domains should be read from a file'
        );
        $this->addOption(
            'log',
            null,
            InputOption::VALUE_REQUIRED,
            'Log to the specified file'
        );
        $this->addOption(
            'slack',
            null,
            InputOption::VALUE_REQUIRED,
            'Add a slack webhook to log to for critical errors.'
        );
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\InputInterface  $input
     * @param  \Symfony\Component\Console\OutputInterface  $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Checking HTTPS integrity of the given domains...');

        $log = $input->getOption('log');
        $domains = $this->getDomainsFromInput(
            $input->getArgument('domains'),
            $input->getOption('file')
        );

        $this->attachCheckerObservers($log, $input->getOption('slack'));

        $failed = $this->checker->run($domains);

        $output->writeln('<info>Checker completed with '.count($failed).' failed domains.</info>');

        if ($log) {
            $output->writeln('Check logs for more info ('.$log.')');
        }
    }

    /**
     * Get the list of domains to check from the given input.
     *
     * @param  string  $input
     * @param  bool  $isFile
     * @return array
     */
    private function getDomainsFromInput($input, $isFile = false)
    {
        if ($isFile) {
            $contents = file_get_contents($input[0]);

            return explode(",", trim($contents));
        } else {
            return $input;
        }
    }

    /**
     * Attach the observers defined in the command.
     *
     * @param  string|null  $log
     * @param  string|null  $slack
     * @return void
     */
    private function attachCheckerObservers($log, $slack)
    {
        if ($log || $slack) {
            $logger = new Logger('https_checker_logger');

            if ($log) {
                $logger->pushHandler(
                    new StreamHandler($log)
                );
            }

            if ($slack) {
                $logger->pushHandler(
                    new SlackWebhookHandler($slack, null, 'HTTPS Checker', true, ':lock:')
                );
            }

            $this->checker->attach(new LogObserver($logger, Logger::CRITICAL));
        }
    }
}
