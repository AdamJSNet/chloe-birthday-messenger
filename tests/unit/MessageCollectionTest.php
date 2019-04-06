<?php

use App\Collections\MessageCollection;
use App\Contracts\MessageInterface;
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

    public function test_it_creates_collection_of_valid_raw_messages()
    {
        $messages = [];
        for ($i = 0; $i < 3; $i++) {
            $testData = array_merge($this->getValidTestData(), [
                "id" => "my_id_$i"
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

        $collection = MessageCollection::fromArray($messages);
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