<?php

use App\Collections\MessageCollection;
use App\Contracts\MessageInterface;
use App\Exceptions\MessageCollectionException;
use App\Traits\Test\MessageDataTestTrait;

class MessageCollectionTest extends PHPUnit\Framework\TestCase
{
    use MessageDataTestTrait;

    public function test_it_accepts_messages()
    {
        $collection = $this->getCollection();

        $this->assertCount(4, $collection);

        $collection->rewind();
        for ($i = 1; $i <= $collection->count(); $i++) {
            $id = "my_id_$i";
            $this->assertEquals($id, $collection->current()->getId());
            $this->assertEquals($id, $collection->key());
            $collection->next();
        }
    }

    public function test_it_updates_a_message()
    {
        $message1 = Mockery::mock(MessageInterface::class)
            ->shouldReceive('getId')
            ->andReturn('my_id_1')
            ->mock();

        $message2 = Mockery::mock(MessageInterface::class)
            ->shouldReceive('getId')
            ->andReturn('my_id_2')
            ->shouldReceive('isSent')
            ->andReturn(false, true)
            ->mock();

        $collection = new MessageCollection();
        $collection->add($message1);
        $collection->add($message2);

        // first isSent call on message2
        $this->assertFalse($collection->rewind()->next()->current()->isSent());

        $collection->update($message2);

        // second isSent call on message2
        $this->assertTrue($collection->rewind()->next()->current()->isSent());
    }

    public function test_it_creates_collection_of_valid_raw_messages()
    {
        $messages = [];
        for ($i = 0; $i < 3; $i++) {
            $testData = array_merge($this->getValidTestData(), [
                "id" => "my_id_$i"  // unique ID per message
            ]);
            $messages[] = $testData;
        }

        $collection = MessageCollection::fromArray($messages);

        $this->assertInstanceOf(MessageCollection::class, $collection);
        $this->assertCount(3, $collection);
    }

    public function test_it_throws_exception_for_invalid_messages()
    {
        $messages = [
            [
                "id" => $this->getValidTestData()["id"]
                // all other message properties are missing
            ]
        ];

        $this->expectException(App\Exceptions\MessageCollectionException::class);

        MessageCollection::fromArray($messages);
    }

    public function test_it_throws_exception_when_adding_an_existing_message()
    {
        $message = $this->getCollection()->current();

        $this->expectExceptionObject(MessageCollectionException::messageExists($message->getId()));

        $collection = new MessageCollection();
        $collection->add($message);
        $collection->add($message);
    }

    public function test_it_throws_exception_when_updating_a_non_existent_message()
    {
        $collection = $this->getCollection();
        $message1 = $collection->current();

        $collection->next();
        $message2 = $collection->current();

        $this->expectExceptionObject(MessageCollectionException::messageNotExists($message2->getId()));

        // create a new collection with message1 only
        $collection = new MessageCollection();
        $collection->add($message1);
        $collection->update($message2);
    }

    public function test_it_filters_by_elapsed()
    {
        $filtered = $this->getCollection()->elapsed();

        $this->assertEquals(2, $filtered->count());

        $expectedIds = ["my_id_1", "my_id_2"];
        $filtered->rewind();

        foreach ($expectedIds as $id) {
            $this->assertEquals($id, $filtered->current()->getId());
            $filtered->next();
        }
    }

    public function test_it_filters_by_notSent()
    {
        $filtered = $this->getCollection()->notSent();

        $this->assertEquals(2, $filtered->count());

        $expectedIds = ["my_id_2", "my_id_4"];
        $filtered->rewind();

        foreach ($expectedIds as $id) {
            $this->assertEquals($id, $filtered->current()->getId());
            $filtered->next();
        }
    }

    public function test_it_filters_by_elapsed_and_notSent()
    {
        $filtered = $this->getCollection()->elapsed()->notSent();

        $this->assertEquals(1, $filtered->count());

        $expectedIds = ["my_id_2"];
        $filtered->rewind();

        foreach ($expectedIds as $id) {
            $this->assertEquals($id, $filtered->current()->getId());
            $filtered->next();
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

        $collection = new MessageCollection();
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
