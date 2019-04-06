<?php

namespace App\Contracts;

interface CommsHandlerClientInterface
{
    public function sendSms(string $to, string $from, string $message): bool;
}