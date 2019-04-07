<?php

use App\Entities\Message;
use App\Enum\MessageType;
use App\Exceptions\MessageException;
use App\Traits\Test\MessageDataTestTrait;

class MessageTest extends PHPUnit\Framework\TestCase
{
    use MessageDataTestTrait;

    public function test_it_rejects_empty_properties_array()
    {
        $missing = ["id", "type", "timestamp", "recipient", "content", "sent"];
        sort($missing);

        $this->expectExceptionObject(MessageException::missingProperties($missing));

        Message::fromArray([]);
    }

    public function test_it_rejects_properties_array_that_contains_too_many_keys()
    {
        $accept = [
            "id" => "my_id",
            "type" => "my_type",
            "timestamp" => "my_timestamp",
            "recipient" => "my_recipient",
            "content" => "my_content",
            "sent" => "my_sent"
        ];

        $reject = [
            "hello" => "bonjour",
            "world" => "monde"
        ];

        $data = array_merge($accept, $reject);

        $this->expectExceptionObject(MessageException::invalidProperties(array_keys($reject)));

        Message::fromArray($data);
    }

    public function test_it_rejects_incomplete_properties_array()
    {
        $data = [
            "id" => "my_id",
            "type" => "my_type",
            "timestamp" => "my_timestamp"
        ];
        $missing = ["recipient", "content", "sent"];
        sort($missing);

        $this->expectExceptionObject(MessageException::missingProperties($missing));

        Message::fromArray($data);
    }

    public function test_it_rejects_timestamp_in_non_date_format()
    {
        $data = $this->getValidTestData();
        $data['timestamp'] = "non_date_format";

        $this->expectExceptionObject(MessageException::invalidDateFormat());

        Message::fromArray($data);
    }

    public function test_it_rejects_timestamp_in_invalid_date_format()
    {
        $data = $this->getValidTestData();
        $data['timestamp'] = "01/01/1970";

        $this->expectExceptionObject(MessageException::invalidDateFormat());

        Message::fromArray($data);
    }

    public function test_it_rejects_invalid_type_value()
    {
        $data = $this->getValidTestData();
        $data['type'] = "invalid_type";

        $this->expectExceptionObject(MessageException::invalidType(MessageType::getAll()));

        Message::fromArray($data);
    }

    public function test_it_generates_a_valid_message_object()
    {
        $data = $this->getValidTestData();
        $message = Message::fromArray($data);

        $this->assertEquals($data["id"], $message->getId());
        $this->assertEquals($data["type"], $message->getType());
        $this->assertEquals(\DateTimeImmutable::createFromFormat(
            "Y-m-d\TH:i:sP",
            $data["timestamp"]
        ), $message->getTimestamp());
        $this->assertEquals($data["recipient"], $message->getRecipient());
        $this->assertEquals($data["content"], $message->getContent());
        $this->assertTrue($message->isSent());
    }

    public function test_it_identifies_an_elapsed_timestamp()
    {
        $data = $this->getValidTestData();
        $message = Message::fromArray($data);

        // 2 days in the future
        $now = $message->getTimestamp()->add(new \DateInterval("P2D"));

        $this->assertTrue($message->isElapsed($now));
    }

    public function test_it_identifies_a_non_elapsed_timestamp()
    {
        $data = $this->getValidTestData();
        $message = Message::fromArray($data);

        // 2 days in the past
        $now = $message->getTimestamp()->sub(new \DateInterval("P2D"));

        $this->assertFalse($message->isElapsed($now));
    }
}