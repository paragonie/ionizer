<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Test;

use ParagonIE\Ionizer\IndexPolicy\{
    AnyIndex,
    IntegersOnly,
    IndexAllowList,
    StringsOnly
};
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;


#[CoversClass(AnyIndex::class)]
#[CoversClass(IntegersOnly::class)]
#[CoversClass(IndexAllowList::class)]
#[CoversClass(StringsOnly::class)]
class IndexPolicyTest extends TestCase
{
    public function testAnyKeys()
    {
        $any = new AnyIndex();
        $this->assertTrue($any->indexIsValid('foo'));
        $this->assertTrue($any->indexIsValid(1));
        $this->assertFalse($any->indexIsValid(1.2));
        $this->assertFalse($any->indexIsValid([]));
        $this->assertFalse($any->indexIsValid(null));
    }

    public function testIntegersOnly()
    {
        $int = new IntegersOnly();
        $this->assertTrue($int->indexIsValid(0));
        $this->assertTrue($int->indexIsValid(1));
        $this->assertTrue($int->indexIsValid(-1));
        $this->assertFalse($int->indexIsValid('1'));
        $this->assertFalse($int->indexIsValid(1.0));
        $this->assertFalse($int->indexIsValid('foo'));
    }

    public function testKeyAllowList()
    {
        $allow = new IndexAllowList('foo', 'bar', 'baz');
        $this->assertTrue($allow->indexIsValid('foo'));
        $this->assertTrue($allow->indexIsValid('bar'));
        $this->assertTrue($allow->indexIsValid('baz'));
        $this->assertFalse($allow->indexIsValid('qux'));

        $allow = new IndexAllowList(1, 2, 3);
        $this->assertTrue($allow->indexIsValid(1));
        $this->assertTrue($allow->indexIsValid(2));
        $this->assertTrue($allow->indexIsValid(3));
        $this->assertFalse($allow->indexIsValid(4));
        $this->assertFalse($allow->indexIsValid('1'));
    }

    public function testStringsOnly()
    {
        $str = new StringsOnly();
        $this->assertTrue($str->indexIsValid('foo'));
        $this->assertTrue($str->indexIsValid('1'));
        $this->assertFalse($str->indexIsValid(1));
        $this->assertFalse($str->indexIsValid(1.0));
    }
}
