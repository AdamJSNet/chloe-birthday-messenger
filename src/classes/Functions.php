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
        // @TODO consider Config class which can be injected/mocked to retrieve env variables
        $basic = new \Nexmo\Client\Credentials\Basic(getenv("NEXMO_API_KEY"), getenv("NEXMO_API_SECRET"));
        $keypair = new \Nexmo\Client\Credentials\Keypair(
            file_get_contents(ROOT_DIR . "/" . getenv("NEXMO_APP_PRIVATE_KEY_PATH")),
            getenv("NEXMO_APP_ID")
        );
        return new \Nexmo\Client(new \Nexmo\Client\Credentials\Container($basic, $keypair));
    }
}
