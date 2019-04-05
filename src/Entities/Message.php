<?php

namespace App\Entities;

use App\Enum\MessageType;

class Message
{
    /** @var $id */
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
        $id,
        string $type,
        \DateTimeInterface $timestamp,
        string $recipient,
        string $message,
        bool $sent = false
    ) {
        $messageTypes = MessageType::getAll();

        if (!in_array($type, array_values($messageTypes))) {
            throw new \InvalidArgumentException("Invalid value of 'type'. Expected " . implode(' / ', $messageTypes));
        }

        $this->id = $id;
        $this->type = $type;
        $this->timestamp = $timestamp;
        $this->recipient = $recipient;
        $this->message = $message;
        $this->sent = $sent;
    }

    /**
     * @return mixed
     */
    public function getId()
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
    public function getRecipient()
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
}
