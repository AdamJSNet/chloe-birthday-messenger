<?php

namespace App\Contracts;

interface MessageInterface
{
    public function getId(): string;
    public function getType(): string;
    public function getTimestamp(): \DateTimeInterface;
    public function getRecipient(): string;
    public function getContent(): string;
    public function isSent(): bool;
    public function setSent(bool $sent): MessageInterface;
    public function isElapsed(\DateTimeInterface $date = null): bool;
    public function toArray(): array;
}