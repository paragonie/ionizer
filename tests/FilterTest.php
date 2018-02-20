<?php

use ParagonIE\Ionizer\Filter\{
    BoolFilter,
    BoolArrayFilter,
    FloatFilter,
    FloatArrayFilter,
    IntFilter,
    IntArrayFilter,
    StringFilter,
    StrictArrayFilter,
    StringArrayFilter,
    WhiteList
};
use ParagonIE\Ionizer\GeneralFilterContainer;

use PHPUnit\Framework\TestCase;

/**
 * Class FilterTest
 */
class FilterTest extends TestCase
{
    /**
     * @covers BoolFilter
     * @throws Error
     */
    public function testBoolFilter()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter('test1', new BoolFilter())
            ->addFilter('test2', new BoolFilter());

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }

        $before = [
            'test1' => 1,
            'test2' => 0
        ];
        $after = $filter($before);

        $this->assertSame(
            [
                'test1' => true,
                'test2' => false
            ],
            $after
        );

        try {
            $typeError = [
                'test1' => true,
                'test2' => []
            ];
            $filter($typeError);
            $this->fail('Expected a TypeError');
        } catch (\TypeError $ex) {
        }
    }

    /**
     * @covers FloatFilter
     * @throws Error
     */
    public function testFloatFilter()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter('test1', new FloatFilter())
            ->addFilter('test2', new FloatFilter())
            ->addFilter('test3', new FloatFilter());

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }

        $before = [
            'test1' => '22.7',
            'test2' => null,
            'test3' => M_E
        ];
        $after = $filter($before);

        $this->assertSame(
            [
                'test1' => 22.7,
                'test2' => 0.0,
                'test3' => M_E
            ],
            $after
        );

        try {
            $typeError = [
                'test1' => '22',
                'test2' => 0,
                'test3' => []
            ];
            $filter($typeError);
            $this->fail('Expected a TypeError');
        } catch (\TypeError $ex) {
        }

        $filter->addFilter(
            'test4',
            (new FloatFilter())
                ->setMinimumValue(1.5)
                ->setMaximumValue(2.47)
        );

        $this->assertSame(
            ['test1' => 0.0,'test2' => 0.0,'test3' => 0.0, 'test4' => 0.0],
            $filter(['test4' => 1.4])
        );
        $this->assertSame(
            ['test1' => 0.0,'test2' => 0.0,'test3' => 0.0, 'test4' => 0.0],
            $filter(['test4' => 1.499])
        );
        $this->assertSame(
            ['test1' => 0.0,'test2' => 0.0,'test3' => 0.0, 'test4' => 1.5],
            $filter(['test4' => 1.5])
        );
        $this->assertSame(
            ['test1' => 0.0,'test2' => 0.0,'test3' => 0.0, 'test4' => 2.0],
            $filter(['test4' => 2.0])
        );
        $this->assertSame(
            ['test1' => 0.0,'test2' => 0.0,'test3' => 0.0, 'test4' => 2.45],
            $filter(['test4' => 2.45])
        );
        $this->assertSame(
            ['test1' => 0.0,'test2' => 0.0,'test3' => 0.0, 'test4' => 2.47],
            $filter(['test4' => 2.47])
        );
        $this->assertSame(
            ['test1' => 0.0,'test2' => 0.0,'test3' => 0.0, 'test4' => 0.0],
            $filter(['test4' => 2.471])
        );
    }

    /**
     * @covers IntFilter
     * @throws Error
     */
    public function testIntFilter()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter('test1', new IntFilter())
            ->addFilter('test2', new IntFilter())
            ->addFilter('test3', new IntFilter());

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }

        $before = [
            'test1' => '22',
            'test2' => null,
            'test3' => PHP_INT_MAX
        ];
        $after = $filter($before);

        $this->assertSame(
            [
                'test1' => 22,
                'test2' => 0,
                'test3' => PHP_INT_MAX
            ],
            $after
        );

        try {
            $typeError = [
                'test1' => '22',
                'test2' => 0,
                'test3' => []
            ];
            $filter($typeError);
            $this->fail('Expected a TypeError');
        } catch (\TypeError $ex) {
        }


        try {
            $typeError = [
                'test1' => '22',
                'test2' => 0,
                'test3' => '1.5'
            ];
            $filter($typeError);
            $this->fail('Expected a TypeError');
        } catch (\TypeError $ex) {
        }


        $filter->addFilter(
            'test4',
            (new IntFilter())
                ->setMinimumValue(15)
                ->setMaximumValue(247)
        );

        $this->assertSame(
            ['test1' => 0,'test2' => 0,'test3' => 0, 'test4' => 0],
            $filter(['test4' => 13])
        );
        $this->assertSame(
            ['test1' => 0,'test2' => 0,'test3' => 0, 'test4' => 0],
            $filter(['test4' => 14])
        );
        $this->assertSame(
            ['test1' => 0,'test2' => 0,'test3' => 0, 'test4' => 15],
            $filter(['test4' => 15])
        );
        $this->assertSame(
            ['test1' => 0,'test2' => 0,'test3' => 0, 'test4' => 20],
            $filter(['test4' => 20])
        );
        $this->assertSame(
            ['test1' => 0,'test2' => 0,'test3' => 0, 'test4' => 245],
            $filter(['test4' => 245])
        );
        $this->assertSame(
            ['test1' => 0,'test2' => 0,'test3' => 0, 'test4' => 247],
            $filter(['test4' => 247])
        );
        $this->assertSame(
            ['test1' => 0,'test2' => 0,'test3' => 0, 'test4' => 0],
            $filter(['test4' => 248])
        );
    }

    /**
     * @covers StringFilter
     * @throws Error
     */
    public function testStringFilter()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter('test1', new StringFilter())
            ->addFilter('test2', new StringFilter())
            ->addFilter('test3', new StringFilter());

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }

        $before = [
            'test1' => 22.7,
            'test2' => null,
            'test3' => 'abcdefg'
        ];
        $after = $filter($before);

        $this->assertSame(
            [
                'test1' => '22.7',
                'test2' => '',
                'test3' => 'abcdefg'
            ],
            $after
        );

        try {
            $typeError = [
                'test1' => '22',
                'test2' => 0,
                'test3' => []
            ];
            $filter($typeError);
            $this->fail('Expected a TypeError');
        } catch (\TypeError $ex) {
        }
    }
}
