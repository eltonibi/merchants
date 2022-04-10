<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function getPspConfiguration($name)
    {

        $config=include("./psp_config.php");
        if ($config[$name]) {
            return $config[$name];
        }
        throw new \Exception("Unregistered PSP {$name} ");
    }

}
