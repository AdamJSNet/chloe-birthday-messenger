<?php

// script for logging nexmo webhook events
$event = json_encode(file_get_contents("php://input")) . "\n";

file_put_contents('../logs/events.log', $event, FILE_APPEND|LOCK_EX);
