<?php

namespace App\Entities;

use App\Enum\MessageType;

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
     * @param string $type
     * @return bool
     * @throws \InvalidArgumentException
     */
    protected function validate(string $type)
    {
        $messageTypes = MessageType::getAll();

        if (!in_array($type, array_values($messageTypes))) {
            throw new \InvalidArgumentException("Invalid value of 'type'. Expected " . implode(' / ', $messageTypes));
        }

        return true;
    }
}
