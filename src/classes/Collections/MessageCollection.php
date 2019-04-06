<?php

namespace App\Collections;

use App\Contracts\MessageInterface;

class MessageCollection implements \Iterator, \Countable
{
    protected $data = [];
    protected $pointer = 0;

    public function current(): MessageInterface
    {
        return $this->data[$this->pointer];
    }

    public function key(): int
    {
        return $this->pointer;
    }

    public function next()
    {
        $this->pointer++;
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    public function valid(): bool
    {
        return isset($this->data[$this->pointer]);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function add(MessageInterface $message)
    {
        $this->data[] = $message;
    }

    public function elapsed(): MessageCollection
    {
        return $this->filter("elapsed");
    }

    public function notSent(): MessageCollection
    {
        return $this->filter("not-sent");
    }

    protected function filter(string $flag): MessageCollection
    {
        $filtered = new MessageCollection();

        /** @var MessageInterface $message */
        foreach ($this->data as $message) {
            switch ($flag) {
                case "elapsed":
                    $valid = $message->isElapsed();
                    break;
                case "not-sent":
                    $valid = !$message->isSent();
                    break;
                default:
                    $valid = false;
                    break;
            }

            if ($valid) {
                $filtered->add($message);
            }
        }

        return $filtered;
    }
}