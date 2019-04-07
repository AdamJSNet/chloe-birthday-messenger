<?php

namespace App\Contracts;

interface CommsHandlerClientInterface
{
    public function sendSms($to, $from, $content): bool;
    public function sendVoice($to, $from, $content): bool;
}