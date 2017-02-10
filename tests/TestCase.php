<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;
use Mockery\MockInterface;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Mock a class and bind it in the IoC container.
     *
     * @param string $class
     * @param mixed $parameters
     *
     * @return \Mockery\MockInterface|$class
     */
    protected function mock($class, $parameters = []) : MockInterface
    {
        $mock = Mockery::mock($class, $parameters);

        $this->app->instance($class, $mock);

        return $mock;
    }
}
