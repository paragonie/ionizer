<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Test;

use ParagonIE\Ionizer\Filter\{
    ArrayFilter,
    BoolFilter,
    BoolArrayFilter,
    FloatFilter,
    FloatArrayFilter,
    IntFilter,
    IntArrayFilter,
    StringFilter,
    StrictArrayFilter,
    StringArrayFilter
};
use Error;
use GenericFilterContainer;
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\InvalidDataException;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


/**
 * Class ArrayFilterTest
 */
#[CoversClass(BoolArrayFilter::class)]
#[CoversClass(FloatArrayFilter::class)]
#[CoversClass(IntArrayFilter::class)]
#[CoversClass(StringArrayFilter::class)]
class ArrayFilterTest extends TestCase
{
    /**
     * @before
     */
    #[Before]
    public function before()
    {
        if (!\class_exists('GenericFilterContainer')) {
            require_once __DIR__ . '/Errata/GenericFilterContainer.php';
        }
    }

    /**
     * This tests the normal use of the array filter, as well as the alternative separators
     *
     * @throws Error
     * @throws InvalidDataException
     */
    public function testArrayFilter()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter('test', new ArrayFilter())
            ->addFilter('test.apple', new BoolFilter())
            ->addFilter('test.boy', new FloatFilter())
            ->addFilter('test.cat', new IntFilter())
            ->addFilter('test.dog', new StringFilter());

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }

        $before = [
            'test' => [
                'apple' => 1,
                'boy' => '1.345',
                'cat' => '25519',
                'dog' => 3.14159265
            ]
        ];
        $this->assertEquals(
            [
                'test' => [
                    'apple' => true,
                    'boy' => 1.345,
                    'cat' => 25519,
                    'dog' => '3.14159265'
                ]
            ],
            $filter($before)
        );

        $wrong = (new GenericFilterContainer())
            ->addFilter('test', new ArrayFilter())
            ->addFilter('test.apple', new BoolFilter())
            ->addFilter('test.boy', new FloatFilter())
            ->addFilter('test.cat', new IntFilter())
            ->addFilter('test.dog', new StringFilter());

        if (!($wrong instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }
        $this->assertEquals(
            [
                'test' => [
                    'apple' => true,
                    'boy' => 1.345,
                    'cat' => 25519,
                    'dog' => '3.14159265'
                ],
                'test.apple' => false,
                'test.boy' => 0.0,
                'test.cat' => 0,
                'test.dog' => ''
            ],
            $wrong($before)
        );

        $corrected = (new GenericFilterContainer())
            ->addFilter('test', new ArrayFilter())
            ->addFilter('test::apple', new BoolFilter())
            ->addFilter('test::boy', new FloatFilter())
            ->addFilter('test::cat', new IntFilter())
            ->addFilter('test::dog', new StringFilter());

        if (!($corrected instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }
        $this->assertEquals(
            [
                'test' => [
                    'apple' => true,
                    'boy' => 1.345,
                    'cat' => 25519,
                    'dog' => '3.14159265'
                ]
            ],
            $corrected($before)
        );
    }

    /**
     * @throws Error
     * @throws InvalidDataException
     */
    public function testBoolArrayFilter()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter('test', new BoolArrayFilter());

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }

        $before = [
            'test' => [
                '',
                null,
                0,
                1,
                'true',
                'false'
            ]
        ];
        $after = $filter($before);

        $this->assertSame(
            [
                'test' => [
                    false,
                    false,
                    false,
                    true,
                    true,
                    true
                ]
            ],
            $after
        );

        try {
            $typeError = [
                'test' => [[]]
            ];
            $filter($typeError);
            $this->fail('Expected a TypeError');
        } catch (\TypeError $ex) {
        }
    }

    /**
     * @throws Error
     * @throws InvalidDataException
     */
    public function testFloatArrayFilter()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter('test', new FloatArrayFilter());

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }

        $before = [
            'test' => [
                null,
                '',
                0,
                33
            ]
        ];
        $after = $filter($before);

        $this->assertSame(
            [
                'test' => [
                    0.0,
                    0.0,
                    0.0,
                    33.0
                ]
            ],
            $after
        );

        try {
            $typeError = [
                'test' => [2, 3, []],
                'test2' => [[1.5]]
            ];
            $filter($typeError);
            $this->fail('Expected a TypeError');
        } catch (\TypeError $ex) {
        }
    }

    /**
     * @throws Error
     * @throws InvalidDataException
     */
    public function testIntArrayFilter()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter('test', new IntArrayFilter())
            ->addFilter('test2', new IntArrayFilter());

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }

        $before = [
            'test' => [
                null,
                '',
                0,
                33
            ],
            'test2' => ['1']
        ];
        $after = $filter($before);

        $this->assertSame(
            [
                'test' => [
                    0,
                    0,
                    0,
                    33
                ],
                'test2' => [
                    1
                ]
            ],
            $after
        );

        try {
            $typeError = [
                'test' => ['1', []],
                'test2' => [[1]]
            ];
            $filter($typeError);
            $this->fail('Expected a TypeError');
        } catch (\TypeError $ex) {
        }
    }

    /**
     * @throws Error
     * @throws Exception
     * @throws InvalidDataException
     */
    public function testStrictArrayFilter()
    {
        try {
            (new GeneralFilterContainer())
                ->addFilter('test', new StrictArrayFilter('float', 'string'));
        } catch (\RuntimeException $ex) {
            $this->assertSame(
                'Cannot accept key types other than "int" or "string".',
                $ex->getMessage()
            );
        }

        $filter = (new GeneralFilterContainer())
            ->addFilter('test', new StrictArrayFilter('int', 'string'));

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }
        $filter([
            'test' => [
                'abc',
                'def'
            ]
        ]);

        try {
            $filter([
                'test' => [
                    1,
                    'abc',
                    'def'
                ]
            ]);
            $this->fail('Uncaught value mismatch');
        } catch (\TypeError $ex) {
            $this->assertSame(
                'Expected an array<int, string>. At least one element of <int, int> was found (test[0] == 1).',
                $ex->getMessage()
            );
        }
        $filter([
            'test' => [
                1 => 'abc',
                2 => 'def'
            ]
        ]);
        try {
            $filter([
                'test' => [
                    1 => 'abc',
                    '1a' => 'def'
                ]
            ]);
            $this->fail('Uncaught value mismatch');
        } catch (\TypeError $ex) {
            $this->assertSame(
                'Expected an array<int, string>. At least one element of <string, string> was found (test["1a"] == "def").',
                $ex->getMessage()
            );
        }

        $filter->addFilter('second', new StrictArrayFilter('string', \stdClass::class));
        $filter([
            'test' => ['abc', 'def'],
            'second' => [
                'test' => (object)['test']
            ]
        ]);
        $filter([
            'test' => ['abc', 'def'],
            'second' => [
                '1234a' => (object)['test']
            ]
        ]);
        try {
            $filter([
                'test' => ['abc', 'def'],
                'second' => [
                    'test' => (object)['test'],
                    123 => (object)['test2'],
                ]
            ]);
            $this->fail('Invalid key accepted');
        } catch (\TypeError $ex) {
            $this->assertSame(
                'Expected an array<string, stdClass>. At least one element of <int, stdClass> was found (second[123] == {"0":"test2"}).',
                $ex->getMessage()
            );
        }
        try {
            $filter([
                'test' => ['abc', 'def'],
                'second' => [
                    'test' => (object)['test'],
                    '123' => null,
                ]
            ]);
            $this->fail('Null accepted where it was not wanted');
        } catch (\TypeError $ex) {
            $this->assertSame(
                'Expected an array<string, stdClass>. At least one element of <int, null> was found (second[123] == null).',
                $ex->getMessage()
            );
        }

        $cf = (new GeneralFilterContainer());
        $cf->addFilter('test', new StrictArrayFilter('string', 'callable'));
        $cf([
            'test' => [
                'a' => function() { return 'foo'; },
                'b' => '\\strlen',
                'c' => [StringFilter::class, 'nonEmpty']
            ],
        ]);

        $fuzz = \bin2hex(\random_bytes(33));
        try {
            $cf([
                'test' => [
                    'a' => function() { return 'foo'; },
                    'b' => $fuzz
                ],
            ]);
            $this->fail('Invalid function name was declared');
        } catch (\TypeError $ex) {
            $this->assertSame(
                'Expected an array<string, callable>. At least one element of <string, string> was found (test["b"] == "' . $fuzz . '").',
                $ex->getMessage()
            );
        }
    }

    /**
     * @throws Error
     * @throws InvalidDataException
     */
    public function testStringArrayFilter()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter('test', new StringArrayFilter());

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }

        $before = [
            'test' => [
                null,
                '',
                0,
                33
            ]
        ];
        $after = $filter($before);

        $this->assertSame(
            [
                'test' => [
                    '',
                    '',
                    '0',
                    '33'
                ]
            ],
            $after
        );

        try {
            $typeError = [
                'test' => ['a', []]
            ];
            $filter($typeError);
            $this->fail('Expected a TypeError');
        } catch (\TypeError $ex) {
        }
    }

}