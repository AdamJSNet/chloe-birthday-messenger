<?php

namespace App\Collections;

use App\Contracts\MessageInterface;
use App\Entities\Message;
use App\Exceptions\MessageCollectionException;
use App\Exceptions\MessageException;

class MessageCollection implements \Iterator, \Countable
{
    protected $data = [];
    protected $pointer = 0;

    public function current(): MessageInterface
    {
        return $this->data[$this->key()];
    }

    public function key()
    {
        return array_keys($this->data)[$this->pointer];
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
        return $this->pointer < count($this->data);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function add(MessageInterface $message)
    {
        $this->data[$message->getId()] = $message;
    }

    /**
     * @return MessageCollection
     */
    public function elapsed(): MessageCollection
    {
        return $this->filter("elapsed");
    }

    /**
     * @return MessageCollection
     */
    public function notSent(): MessageCollection
    {
        return $this->filter("not-sent");
    }

    /**
     * @param array $data
     * @return MessageCollection
     * @throws MessageCollectionException
     */
    public static function fromArray(array $data): MessageCollection
    {
        $collection = new self();
        foreach ($data as $index => $item) {
            try {
                $item["id"] = isset($item["id"]) ? $item["id"] : $index;
                $collection->add(Message::fromArray($item));
            } catch (MessageException $e) {
                throw MessageCollectionException::invalidMessage($e);
            }
        }
        return $collection;
    }

    /**
     * @param string $flag
     * @return MessageCollection
     */
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