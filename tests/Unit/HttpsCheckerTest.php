<?php

namespace Tests\Unit;

use Gigasavvy\HttpsChecker\HttpsChecker;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Tests\TestCase;

class HttpsCheckerTest extends TestCase
{
    /** @test */
    public function itMakesRequests()
    {
        $transactions = [];
        $client = $this->getMockClient([
            new Response(200),
            new Response(200),
        ], $transactions);

        $checker = new HttpsChecker($client);
        $domains = [
            'https://google.com',
            'https://gigasavvy.com',
        ];
        $checker->run($domains);

        $this->assertCount(2, $transactions);

        foreach ($transactions as $i => $transaction) {
            $request = $transaction['request'];

            $this->assertEquals($domains[$i], (string) $request->getUri());
        }
    }

    /** @test */
    public function secureConnectionIsVerified()
    {
        $client = $this->getMockClient([
            new Response(200),
            new Response(200),
        ]);

        $checker = new HttpsChecker($client);

        $failed = $checker->run([
            'https://google.com',
            'https://gigasavvy.com',
        ]);

        $this->assertInternalType('array', $failed);
        $this->assertEmpty($failed);
    }

    /** @test */
    public function insecureConnectionIsUnverified()
    {
        $client = $this->getMockClient([
            new ConnectException('Failed to connect.', new Request('GET', 'test')),
        ]);

        $checker = new HttpsChecker($client);
        $failed = $checker->run(['https://foo.bar']);

        $this->assertCount(1, $failed);
        $this->assertEquals('https://foo.bar', $failed[0]);
    }

    /** @test */
    public function insecureConnectionsGetLogged()
    {
        $client = $this->getMockClient([
            new ConnectException('Failed to connect.', new Request('GET', 'test')),
        ]);
        $logger = $this->getMockLogger();

        $checker = new HttpsChecker($client, $logger);
        $checker->run(['https://foo.bar']);

        $this->assertTrue(
            $logger->getHandlers()[0]->hasRecord(
                'HTTPS validation failed for site: https://foo.bar',
                Logger::CRITICAL
            )
        );
    }

    /**
     * Get a mock HTTP client for testing.
     *
     * @param  array  $responses
     * @param  array  &$transactions
     * @return \GuzzleHttp\Client
     */
    private function getMockClient($responses = [], &$transactions = [])
    {
        $stack = MockHandler::createWithMiddleware($responses);
        $stack->push(
            Middleware::history($transactions)
        );

        return new Client(['handler' => $stack]);
    }

    /**
     * Get a mock logger for testing.
     *
     * @return \Monolog\Logger;
     */
    private function getMockLogger()
    {
        $logger = new Logger('test_logger');
        $logger->pushHandler(new TestHandler());

        return $logger;
    }
}
