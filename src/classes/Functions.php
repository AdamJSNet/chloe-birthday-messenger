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

    public static function getNexmoClient()
    {
        $basic = new \Nexmo\Client\Credentials\Basic(getenv("NEXMO_API_KEY"), getenv("NEXMO_API_SECRET"));
        return new \Nexmo\Client($basic);
    }
}
