<?php

namespace Tests\Support\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;

trait MocksHttp
{
    /**
     * Make a new mock HTTP client for testing.
     *
     * @param  array  $responses
     * @param  array  &$transactions
     * @return \GuzzleHttp\Client
     */
    protected function makeHttpClient($responses = [], &$transactions = [])
    {
        $stack = MockHandler::createWithMiddleware($responses);
        $stack->push(
            Middleware::history($transactions)
        );

        return new Client(['handler' => $stack]);
    }
}
