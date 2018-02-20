<?php

use ParagonIE\Ionizer\Filter\{
    BoolArrayFilter,
    FloatArrayFilter,
    IntArrayFilter,
    StringFilter,
    StrictArrayFilter,
    StringArrayFilter
};
use ParagonIE\Ionizer\GeneralFilterContainer;
use PHPUnit\Framework\TestCase;


/**
 * Class ArrayFilterTest
 */
class ArrayFilterTest extends TestCase
{
    /**
     * @covers BoolArrayFilter
     * @throws Error
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
     * @covers FloatArrayFilter
     * @throws Error
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
     * @covers IntArrayFilter
     * @throws Error
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
     * @covers StringArrayFilter
     * @throws Error
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