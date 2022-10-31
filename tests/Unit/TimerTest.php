<?php

namespace Ahoiroman\Pong\Tests\Unit;

use Ahoiroman\Pong\Exceptions\TimerNotStartedException;
use Ahoiroman\Pong\Tests\TestCase;
use Ahoiroman\Pong\Timer;

class TimerTest extends TestCase
{
    protected Timer $timer;

    public function setUp(): void
    {
        $this->timer = new Timer();
    }

    /**
     * @test
     */
    public function it_can_create_a_new_instance()
    {
        $this->assertInstanceOf(Timer::class, $this->timer);
    }

    /**
     * @test
     */
    public function it_can_start_the_timer()
    {
        $this->assertIsFloat($this->timer->start());
    }

    /**
     * @test
     */
    public function it_can_stop_the_timer()
    {
        $this->timer->start();

        $this->assertIsFloat($this->timer->stop());
    }

    /**
     * @test
     *
     * @throws TimerNotStartedException
     */
    public function it_throws_an_exception()
    {
        $this->expectException(TimerNotStartedException::class);

        $this->timer->stop();
    }

    /**
     * @test
     */
    public function it_returns_an_object()
    {
        $this->timer->start();
        $this->timer->stop();

        $this->assertIsObject($this->timer->getResults());
    }

    /**
     * @test
     */
    public function it_stop_the_timer_when_get_results()
    {
        $this->timer->start();

        $this->assertIsObject($this->timer->getResults());
    }

    /**
     * @test
     */
    public function it_get_the_same_start_value()
    {
        $start = $this->timer->start();
        $stop = $this->timer->stop();

        $results = $this->timer->getResults();

        $this->assertSame($start, $results->start->as_float);
    }

    /**
     * @test
     */
    public function it_get_the_same_stop_value()
    {
        $start = $this->timer->start();
        $stop = $this->timer->stop();

        $results = $this->timer->getResults();

        $this->assertSame($stop, $results->stop->as_float);
    }
}
