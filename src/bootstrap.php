<?php

define("ROOT_DIR", dirname(__DIR__));

require_once(ROOT_DIR . "/vendor/autoload.php");

// configure console logger
$log = new Monolog\Logger("log");

$logHandler = new Monolog\Handler\StreamHandler(ROOT_DIR . "/logs/script.log", Monolog\Logger::INFO);
$logFormatter = new Monolog\Formatter\LineFormatter("[%datetime%] %level_name%: %message% %context%\n", "Y-m-d H:i:sP", true, true);
$logHandler->setFormatter($logFormatter);

$log->pushHandler($logHandler);

try {
    $dotenv = Dotenv\Dotenv::create(ROOT_DIR);
    $dotenv->load();
    $dotenv->required(["MESSAGES_FILENAME", "NEXMO_API_KEY", "NEXMO_API_SECRET", "NEXMO_VOICE_SENDER", "NEXMO_SMS_SENDER", "NEXMO_APP_PRIVATE_KEY_PATH"]);

    $timezone = getenv("TIMEZONE");
    $log->setTimezone(new DateTimeZone(empty($timezone) ? "UTC" : $timezone));
} catch (Exception $e) {
    $log->error("*** ABORTED *** " . $e->getMessage());
    die();
}
