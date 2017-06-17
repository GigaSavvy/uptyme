<?php

namespace Gigasavvy\HttpsChecker;

use Gigasavvy\HttpsChecker\Observer\Observable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

class HttpsChecker extends Observable
{
    /**
     * The HTTP client.
     *
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * Create a new instance.
     *
     * @param  \GuzzleHttp\Client  $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
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
                $this->notify('HTTPS validation failed for site: '.$domain, [
                    'domain' => $domain,
                ]);

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
}
