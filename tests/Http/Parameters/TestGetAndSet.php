<?php
namespace Wandu\Http\Parameters;

trait TestGetAndSet
{
    public function testSet()
    {
        static::assertNull($this->param1->get('added'));
        static::assertFalse($this->param1->has('added'));

        $this->param1->set('added', "added!!");

        static::assertEquals('added!!', $this->param1->get('added'));
        static::assertTrue($this->param1->has('added'));
    }

    public function testRemove()
    {
        static::assertNull($this->param1->get('added'));
        static::assertFalse($this->param1->has('added'));

        static::assertSame('string!', $this->param1->get('string'));
        static::assertTrue($this->param1->has('string'));

        $this->param1->set('added', "added!!");
        $this->param1->remove('added');

        static::assertNull($this->param1->get('added'));
        static::assertFalse($this->param1->has('added'));

        $this->param1->remove('string');

        static::assertNull($this->param1->get('string'));
        static::assertFalse($this->param1->has('string'));
    }

    public function testArrayAccessOffsetSet()
    {
        static::assertNull($this->param1->get('added'));
        static::assertFalse($this->param1->has('added'));

        $this->param1['added'] = 'added!!';

        static::assertSame('added!!', $this->param1->get('added'));
        static::assertTrue($this->param1->has('added'));
    }

    public function testArrayAccessOffsetUnset()
    {
        static::assertSame('string!', $this->param1->get('string'));
        static::assertTrue($this->param1->has('string'));

        unset($this->param1['string']);

        static::assertNull($this->param1->get('string'));
        static::assertFalse($this->param1->has('string'));
    }

    public function testGetIteratorWithSet()
    {
        $this->param1->set('added', 'added!!');
        static::assertSame([
            'string',
            'number',
            'array',
            'array_of_array',
            'added',
        ], array_keys(iterator_to_array($this->param1)));
        static::assertSame([
            'string!',
            '10',
            [
                'null' => null,
                'empty' => '',
                'false' => false,
            ],
            [
                [
                    'string' => 'string!',
                    'number' => '10',
                ],
                [
                    'string' => 'string!!',
                    'number' => '11',
                ],
                [
                    'string' => 'string!!!',
                    'number' => '12',
                ],
            ],
            'added!!',
        ], array_values(iterator_to_array($this->param1)));
    }

    public function testGetIteratorWithRemove()
    {
        $this->param1->remove('number');
        static::assertSame([
            'string',
            'array',
            'array_of_array',
        ], array_keys(iterator_to_array($this->param1)));
        static::assertSame([
            'string!',
            [
                'null' => null,
                'empty' => '',
                'false' => false,
            ],
            [
                [
                    'string' => 'string!',
                    'number' => '10',
                ],
                [
                    'string' => 'string!!',
                    'number' => '11',
                ],
                [
                    'string' => 'string!!!',
                    'number' => '12',
                ],
            ],
        ], array_values(iterator_to_array($this->param1)));
    }
}
