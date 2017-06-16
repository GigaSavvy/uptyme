<?php

namespace Gigasavvy\HttpsChecker\Commands;

use Gigasavvy\HttpsChecker\HttpsChecker;
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

        $failed = $this->getChecker($log)->run($domains);

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
     * Get the configured checker.
     *
     * @return \Gigasavvy\HttpsChecker\HttpsChecker
     */
    private function getChecker($log)
    {
        $logger = new Logger('https_checker_logger');

        if ($log) {
            $logger->pushHandler(
                new StreamHandler($log)
            );
        }

        $logger->pushHandler(
            new SlackWebhookHandler(
                'https://hooks.slack.com/services/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',
                null,
                'HTTPS Checker',
                true,
                ':lock:'
            )
        );

        return new HttpsChecker(new HttpClient(), $logger);
    }
}
