<?php

namespace App\Traits\Test;

use App\Enum\MessageType;

trait MessageDataTestTrait
{
    protected function getValidTestData()
    {
        return [
            "id" => "my_id",
            "type" => MessageType::TYPE_SMS,
            "timestamp" => (new \DateTimeImmutable())->format("Y-m-d\TH:i:sP"),
            "recipient" => "+447012345678",
            "content" => "Hello World",
            "sent" => true
        ];
    }
}
