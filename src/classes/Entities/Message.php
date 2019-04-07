<?php

namespace App\Entities;

use App\Contracts\MessageInterface;
use App\Enum\MessageType;
use App\Exceptions\MessageException;

class Message implements MessageInterface
{
    /** @var string $id */
    protected $id;
    /** @var string $type */
    protected $type;
    /** @var \DateTimeInterface  $timestamp */
    protected $timestamp;
    /** @var string $recipient */
    protected $recipient;
    /** @var string $content */
    protected $content;
    /** @var bool $sent */
    protected $sent;

    public function __construct(
        string $id,
        string $type,
        \DateTimeInterface $timestamp,
        string $recipient,
        string $content,
        bool $sent = false
    ) {
        $this->validate($type);

        $this->id = trim($id);
        $this->type = trim($type);
        $this->timestamp = $timestamp;
        $this->recipient = trim($recipient);
        $this->content = trim($content);
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
    public function getContent(): string
    {
        return $this->content;
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
    public function setSent(bool $sent): MessageInterface
    {
        $this->sent = $sent;
        return $this;
    }

    /**
     * @param \DateTimeInterface $date
     * @return bool
     */
    public function isElapsed(\DateTimeInterface $date = null): bool
    {
        if ($date === null) {
            $date = new \DateTimeImmutable();
        }
        return ($date > $this->getTimestamp());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "id" => $this->getId(),
            "type" => $this->getType(),
            "timestamp" => $this->getTimestamp()->format("Y-m-d\TH:i:sP"),
            "recipient" => $this->getRecipient(),
            "content" => $this->getContent(),
            "sent" => $this->isSent()
        ];
    }

    /**
     * @param array $data
     * @return Message
     * @throws MessageException
     */
    public static function fromArray(array $data): Message
    {
        $props = ["id", "type", "timestamp", "recipient", "content", "sent"];
        $keys = array_keys($data);

        sort($props);
        sort($keys);

        $diff1 = array_diff($props, $keys);
        $diff2 = array_diff($keys, $props);

        if (!empty($diff1)) {
            throw MessageException::missingProperties($diff1);
        }

        if (!empty($diff2)) {
            throw MessageException::invalidProperties($diff2);
        }

        extract($data);

        // convert timestamp to object
        $dateTime = \DateTimeImmutable::createFromFormat("Y-m-d\TH:i:sP", $timestamp);
        if (!($dateTime instanceof \DateTimeImmutable)) {
            throw MessageException::invalidDateFormat();
        }

        // cast to boolean
        $boolSent = (bool) $sent;

        // json encode content if array
        switch (gettype($content)) {
            case "array":
            case "object":
                $content = json_encode($content);
                break;
            default:
                break;
        }

        return new self($id, $type, $dateTime, $recipient, $content, $boolSent);
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
