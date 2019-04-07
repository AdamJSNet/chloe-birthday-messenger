<?php

define("ROOT_DIR", dirname(__DIR__));

require_once(ROOT_DIR . "/vendor/autoload.php");

// configure console logger
$console = new Monolog\Logger("stdout");

$stdOutHandler = new Monolog\Handler\StreamHandler("php://stdout", Monolog\Logger::INFO);
$stdOutFormatter = new Monolog\Formatter\LineFormatter("[%datetime%] %level_name%: %message% %context%\n", "Y-m-d H:i:sP", true, true);
$stdOutHandler->setFormatter($stdOutFormatter);

$console->pushHandler($stdOutHandler);

try {
    $dotenv = Dotenv\Dotenv::create(ROOT_DIR);
    $dotenv->load();
    $dotenv->required(["MESSAGES_FILENAME", "NEXMO_API_KEY", "NEXMO_API_SECRET", "NEXMO_SENDER", "NEXMO_APP_PRIVATE_KEY_PATH"]);

    $timezone = getenv("TIMEZONE");
    $console->setTimezone(new DateTimeZone(empty($timezone) ? "UTC" : $timezone));
} catch (Exception $e) {
    $console->error("*** ABORTED *** " . $e->getMessage());
    die();
}
