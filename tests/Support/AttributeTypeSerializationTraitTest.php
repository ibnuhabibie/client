<?php

namespace Laracatch\Client\Tests\Support;

use Illuminate\Support\Str;
use Laracatch\Client\Support\AttributeTypeSerializationTrait;
use Laracatch\Client\Tests\TestCase;

class AttributeTypeSerializationTraitTest extends TestCase
{
    use AttributeTypeSerializationTrait;

    /**
     * @dataProvider provider
     * @test
     */
    public function it_should_serialize_values($input, $result)
    {
        $this->assertEquals($result, $this->serializeValue($input));
    }

    public function provider()
    {
        $trimString = Str::random(510);

        return [
            [['item'], ['item']],
            [$this, __CLASS__],
            ['short string', 'short string'],
            [$trimString, substr($trimString, 0, 500) . '...'],
            [314, '314'],
            [3.14, '3.14'],
            [3.14, '3.14'],
            [true, 'true'],
            [false, 'false'],
            [null, 'NULL']
        ];
    }
}
