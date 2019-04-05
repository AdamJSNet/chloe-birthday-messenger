<?php

namespace App\Contracts;

interface MessageInterface
{
    public function getId(): string;
    public function getType(): string;
    public function getTimestamp(): \DateTimeInterface;
    public function getRecipient(): string;
    public function getMessage(): string;
    public function isSent(): bool;
    public function setSent(bool $sent): MessageInterface;
    public function isElapsed(\DateTimeInterface $date): bool;
}