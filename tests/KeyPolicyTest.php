<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Test;

use ParagonIE\Ionizer\KeyPolicy\{
    AnyKeys,
    IntegersOnly,
    KeyAllowList,
    StringsOnly
};
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;


#[CoversClass(AnyKeys::class)]
#[CoversClass(IntegersOnly::class)]
#[CoversClass(KeyAllowList::class)]
#[CoversClass(StringsOnly::class)]
class KeyPolicyTest extends TestCase
{
    public function testAnyKeys()
    {
        $any = new AnyKeys();
        $this->assertTrue($any->keyIsValid('foo'));
        $this->assertTrue($any->keyIsValid(1));
        $this->assertFalse($any->keyIsValid(1.2));
        $this->assertFalse($any->keyIsValid([]));
        $this->assertFalse($any->keyIsValid(null));
    }

    public function testIntegersOnly()
    {
        $int = new IntegersOnly();
        $this->assertTrue($int->keyIsValid(0));
        $this->assertTrue($int->keyIsValid(1));
        $this->assertTrue($int->keyIsValid(-1));
        $this->assertFalse($int->keyIsValid('1'));
        $this->assertFalse($int->keyIsValid(1.0));
        $this->assertFalse($int->keyIsValid('foo'));
    }

    public function testKeyAllowList()
    {
        $allow = new KeyAllowList('foo', 'bar', 'baz');
        $this->assertTrue($allow->keyIsValid('foo'));
        $this->assertTrue($allow->keyIsValid('bar'));
        $this->assertTrue($allow->keyIsValid('baz'));
        $this->assertFalse($allow->keyIsValid('qux'));

        $allow = new KeyAllowList(1, 2, 3);
        $this->assertTrue($allow->keyIsValid(1));
        $this->assertTrue($allow->keyIsValid(2));
        $this->assertTrue($allow->keyIsValid(3));
        $this->assertFalse($allow->keyIsValid(4));
        $this->assertFalse($allow->keyIsValid('1'));
    }

    public function testStringsOnly()
    {
        $str = new StringsOnly();
        $this->assertTrue($str->keyIsValid('foo'));
        $this->assertTrue($str->keyIsValid('1'));
        $this->assertFalse($str->keyIsValid(1));
        $this->assertFalse($str->keyIsValid(1.0));
    }
}
