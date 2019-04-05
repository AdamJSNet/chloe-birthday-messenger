<?php

use App\Contracts\MessageInterface;

class MessageCollectionTest extends PHPUnit\Framework\TestCase
{
    public function test_it_accepts_messages()
    {
        $collection = $this->getCollection();

        $this->assertEquals(4, $collection->count());

        $collection->rewind();
        for ($i = 1; $i <= $collection->count(); $i++) {
            $this->assertEquals("my_id_$i", $collection->current()->getId());
            $collection->next();
        }
    }

    protected function getCollection()
    {
        // elapsed, sent
        $message1 = Mockery::mock(MessageInterface::class)
            ->shouldReceive("getId")
            ->andReturn("my_id_1")
            ->shouldReceive("isElapsed")
            ->andReturn(true)
            ->shouldReceive("isSent")
            ->andReturn(true)
            ->mock();

        // elapsed, not sent
        $message2 = Mockery::mock(MessageInterface::class)
            ->shouldReceive("getId")
            ->andReturn("my_id_2")
            ->shouldReceive("isElapsed")
            ->andReturn(true)
            ->shouldReceive("isSent")
            ->andReturn(false)
            ->mock();

        // not elapsed, sent
        $message3 = Mockery::mock(MessageInterface::class)
            ->shouldReceive("getId")
            ->andReturn("my_id_3")
            ->shouldReceive("isElapsed")
            ->andReturn(false)
            ->shouldReceive("isSent")
            ->andReturn(true)
            ->mock();

        // not elapsed, not sent
        $message4 = Mockery::mock(MessageInterface::class)
            ->shouldReceive("getId")
            ->andReturn("my_id_4")
            ->shouldReceive("isElapsed")
            ->andReturn(false)
            ->shouldReceive("isSent")
            ->andReturn(false)
            ->mock();

        $collection = new \App\Collections\MessageCollection();
        $collection->add($message1);
        $collection->add($message2);
        $collection->add($message3);
        $collection->add($message4);

        return $collection;
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}