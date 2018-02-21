<?php

use ParagonIE\Ionizer\Filter\WhiteList;
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\InvalidDataException;
use PHPUnit\Framework\TestCase;


/**
 * Class WhiteListTest
 */
class WhiteListTest extends TestCase
{
    /**
     * @covers WhiteList
     * @throws Error
     * @throws InvalidDataException
     */
    public function testWhiteList()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter(
                'test1',
                (new WhiteList(
                    'abc',
                    'def',
                    'ghi'
                ))->setDefault('jkl')
            );

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }

        $before = [
            'test1' => 'abc'
        ];
        $after = $filter($before);

        $this->assertSame(
            [
                'test1' => 'abc'
            ],
            $after
        );

        $before = [
            'test1' => 0.123
        ];
        $after = $filter($before);

        $this->assertSame(
            [
                'test1' => 'jkl'
            ],
            $after
        );
    }
}