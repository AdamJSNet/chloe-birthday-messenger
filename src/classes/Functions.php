<?php

namespace App;

use Monolog\Logger;

class Functions
{
    public static function pluralise(int $qty, string $singular, string $plural)
    {
        if ($qty === 1) {
            return $singular;
        }
        return $plural;
    }

    public static function end(Logger $logger = null)
    {
        if ($logger instanceof Logger) {
            $logger->info("-------------------------");
        }
        die();
    }
}
