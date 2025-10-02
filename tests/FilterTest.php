<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Test;

use ParagonIE\Ionizer\Filter\{
    BoolFilter,
    FloatFilter,
    IntFilter,
    StringFilter
};
use Error;
use ParagonIE\Ionizer\InvalidDataException;
use ParagonIE\Ionizer\GeneralFilterContainer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class FilterTest
 */
#[CoversClass(BoolFilter::class)]
#[CoversClass(IntFilter::class)]
#[CoversClass(FloatFilter::class)]
#[CoversClass(StringFilter::class)]
class FilterTest extends TestCase
{
    /**
     * @throws Error
     * @throws InvalidDataException
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
     * @throws Error
     * @throws InvalidDataException
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
     * @throws Error
     * @throws InvalidDataException
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
     * @throws Error
     * @throws InvalidDataException
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

    /**
     * @throws InvalidDataException
     */
    public function testStringRegex()
    {
        $filter = new GeneralFilterContainer();
        $filter->addFilter(
            'test1',
            (new StringFilter())->setPattern('^[a-z]+$')
        );
        $after = $filter(['test1' => 'abcdef']);

        $this->assertSame(
            ['test1' => 'abcdef'],
            $after
        );
    }
}
