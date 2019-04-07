<?php

namespace App\Contracts;

interface CommsHandlerClientInterface
{
    public function sendSms($to, $from, $content): bool;
}