<?php

use App\Entities\Message;
use App\Enum\MessageType;
use App\Exceptions\MessageException;

class MessageTest extends PHPUnit\Framework\TestCase
{
    public function test_it_rejects_empty_properties_array()
    {
        $invalid = ["id", "type", "timestamp", "recipient", "message", "sent"];
        sort($invalid);

        $this->expectExceptionObject(MessageException::invalidProperties($invalid));

        Message::fromArray([]);
    }

    public function test_it_rejects_incomplete_properties_array()
    {
        $accept = [
            "id" => "my_id",
            "type" => "my_type",
            "timestamp" => "my_timestamp",
            "recipient" => "my_recipient",
            "message" => "my_message",
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

    public function test_it_rejects_properties_array_that_contains_too_many_keys()
    {
        $data = [
            "id" => "my_id",
            "type" => "my_type",
            "timestamp" => "my_timestamp"
        ];
        $invalid = ["recipient", "message", "sent"];
        sort($invalid);

        $this->expectExceptionObject(MessageException::invalidProperties($invalid));

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
        $this->assertEquals($data["message"], $message->getMessage());
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

    protected function getValidTestData()
    {
        return [
            "id" => "my_id",
            "type" => MessageType::TYPE_SMS,
            "timestamp" => (new \DateTimeImmutable())->format("Y-m-d\TH:i:sP"),
            "recipient" => "+447012345678",
            "message" => "Hello World",
            "sent" => true
        ];
    }
}