<?php

namespace Gigasavvy\HttpsChecker;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Psr\Log\LoggerInterface as Logger;

class HttpsChecker
{
    /**
     * The HTTP client.
     *
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * The logger for logging failed domains.
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Create a new instance.
     *
     * @param  \GuzzleHttp\Client  $client
     * @param  \Psr\Log\LoggerInterface|null  $logger
     */
    public function __construct(Client $client, Logger $logger = null)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * Run checks on the given domains.
     *
     * @param  array  $domains
     * @return array  An array of failed domains.
     */
    public function run(array $domains = [])
    {
        $failed = [];

        foreach ($domains as $domain) {
            if (! $this->validate($domain)) {
                $this->logFailed($domain);

                $failed[] = $domain;
            }
        }

        return $failed;
    }

    /**
     * Validate the given domain.
     *
     * @param  string  $domain
     * @return bool
     */
    private function validate($domain)
    {
        try {
            $response = $this->client->get($domain);
        } catch (ConnectException $e) {
            return false;
        }

        return true;
    }

    /**
     * Log the given domain as failed.
     *
     * @param  string  $domain
     * @return void
     */
    private function logFailed($domain)
    {
        if (! is_null($this->logger)) {
            $this->logger->critical('HTTPS validation failed for site: '.$domain);
        }
    }
}
