<?php

namespace Tests\Unit;

use Gigasavvy\HttpsChecker\HttpsChecker;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\Support\Concerns\MocksHttp;
use Tests\Support\Mocks\MockObserver;
use Tests\TestCase;

class HttpsCheckerTest extends TestCase
{
    use MocksHttp;

    /** @test */
    public function itMakesRequests()
    {
        $transactions = [];
        $client = $this->makeHttpClient([
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
        $client = $this->makeHttpClient([
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
        $client = $this->makeHttpClient([
            new ConnectException('Failed to connect.', new Request('GET', 'test')),
        ]);

        $checker = new HttpsChecker($client);
        $failed = $checker->run(['https://foo.bar']);

        $this->assertCount(1, $failed);
        $this->assertEquals('https://foo.bar', $failed[0]);
    }

    /** @test */
    public function checkerIsObservable()
    {
        $client = $this->makeHttpClient([
            new ConnectException('Failed to connect.', new Request('GET', 'test')),
        ]);

        $checker = new HttpsChecker($client);
        $observer = new MockObserver();

        $checker->attach($observer);
        $checker->run(['https://foo.baz']);

        $this->assertCount(1, $observer->messages());
        $this->assertEquals(
            'HTTPS validation failed for site: https://foo.baz',
            $observer->messages()[0]
        );
    }
}
