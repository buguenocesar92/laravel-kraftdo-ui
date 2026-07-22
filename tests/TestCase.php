<?php

namespace Kraftdo\Ui\Tests;

use Kraftdo\Ui\KraftdoUiServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /** @return array<int, class-string> */
    protected function getPackageProviders($app): array
    {
        return [KraftdoUiServiceProvider::class];
    }
}
