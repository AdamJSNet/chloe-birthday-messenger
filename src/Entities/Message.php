<?php

namespace App\Entities;

use App\Enum\MessageType;
use App\Exceptions\MessageException;

class Message
{
    /** @var string $id */
    protected $id;
    /** @var string $type */
    protected $type;
    /** @var \DateTimeInterface  $timestamp */
    protected $timestamp;
    /** @var string $recipient */
    protected $recipient;
    /** @var string $message */
    protected $message;
    /** @var bool $sent */
    protected $sent;

    public function __construct(
        string $id,
        string $type,
        \DateTimeInterface $timestamp,
        string $recipient,
        string $message,
        bool $sent = false
    ) {
        $this->validate($type);

        $this->id = trim($id);
        $this->type = trim($type);
        $this->timestamp = $timestamp;
        $this->recipient = trim($recipient);
        $this->message = trim($message);
        $this->sent = $sent;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getTimestamp(): \DateTimeInterface
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->sent;
    }

    /**
     * @param bool $sent
     * @return $this
     */
    public function setSent(bool $sent): self
    {
        $this->sent = $sent;
        return $this;
    }

    /**
     * @param array $data
     * @return Message
     * @throws MessageException
     */
    public static function fromArray(array $data)
    {
        $props = ["id", "type", "timestamp", "recipient", "message", "sent"];
        $keys = array_keys($data);

        sort($props);
        sort($data);

        $diff1 = array_diff($props, $keys);
        $diff2 = array_diff($keys, $props);

        if (!empty($diff1)) {
            throw MessageException::invalidProperties($diff1);
        }

        if (!empty($diff2)) {
            throw MessageException::invalidProperties($diff2);
        }

        extract($data);

        // convert timestamp to object
        $dateTime = \DateTimeImmutable::createFromFormat($timestamp, "Y-m-d\TH:i:sP");
        if (!($dateTime instanceof \DateTimeImmutable)) {
            throw MessageException::invalidDateFormat();
        }

        // cast to boolean
        $boolSent = (bool) $sent;

        return new self($id, $type, $dateTime, $recipient, $message, $boolSent);
    }

    /**
     * @param string $type
     * @return bool
     * @throws MessageException
     */
    protected function validate(string $type)
    {
        $messageTypes = MessageType::getAll();

        if (!in_array($type, array_values($messageTypes))) {
            throw MessageException::invalidType($messageTypes);
        }

        return true;
    }
}
