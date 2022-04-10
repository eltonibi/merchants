<?php

namespace App\Payments\Billing\Gateways;

use Symfony\Component\VarDumper\VarDumper;

class Base
{
    /**
     * Return an Object type Gateway based on given name
     * @param null $name
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public static function gateway($name = null, $options = array())
    {
        $gateway = "\\App\\Payments\\Billing\\Gateways\\" .$name;

        if (class_exists($gateway)) {
            return new $gateway($options);
        }

        throw new \Exception("Unable to load class: {$gateway}.");
    }
}
