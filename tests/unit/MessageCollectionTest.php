<?php

use App\Collections\MessageCollection;
use App\Contracts\MessageInterface;

class MessageCollectionTest extends PHPUnit\Framework\TestCase
{
    public function test_it_accepts_messages()
    {
        $message1 = Mockery::mock(MessageInterface::class)
            ->shouldReceive("getId")
            ->andReturn("my_id_1")
            ->mock();

        $message2 = Mockery::mock(MessageInterface::class)
            ->shouldReceive("getId")
            ->andReturn("my_id_2")
            ->mock();

        $message3 = Mockery::mock(MessageInterface::class)
            ->shouldReceive("getId")
            ->andReturn("my_id_3")
            ->mock();

        $collection = new MessageCollection();
        $collection->add($message1);
        $collection->add($message2);
        $collection->add($message3);

        $this->assertEquals(3, $collection->count());

        $collection->rewind();
        for ($i = 1; $i <= $collection->count(); $i++) {
            $this->assertEquals("my_id_$i", $collection->current()->getId());
            $collection->next();
        }
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}