<?php

namespace Tests\Unit\Observer;

use Tests\Support\Mocks\MockObservable;
use Tests\Support\Mocks\MockObserver;
use Tests\TestCase;

class ObservableTest extends TestCase
{
    /** @test */
    public function attach()
    {
        $observer = new MockObserver();
        $observable = new MockObservable();

        $observable->attach($observer);

        $this->assertCount(1, $observable->observers());
        $this->assertSame($observer, $observable->observers()[0]);
    }

    /** @test */
    public function notify()
    {
        $observer = new MockObserver();
        $observable = new MockObservable();

        $observable->attach($observer);

        $observable->notify('This is a message.');

        $this->assertCount(1, $observer->messages());
        $this->assertEquals(
            'This is a message.',
            $observer->messages()[0]
        );
    }

    /** @test */
    public function detatch()
    {
        $observer = new MockObserver();
        $observable = new MockObservable();

        $observable->attach($observer);
        $observable->detach($observer);

        $this->assertEmpty($observable->observers());
    }
}
