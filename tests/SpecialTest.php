<?php

use ParagonIE\Ionizer\Filter\Special\CreditCardNumberFilter;
use ParagonIE\Ionizer\GeneralFilterContainer;
use PHPUnit\Framework\TestCase;


/**
 * Class SpecialTest
 */
class SpecialTest extends TestCase
{
    /**
     * @throws Error
     */
    public function testCreditCardNumberFilter()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter('cc', new CreditCardNumberFilter());

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }

        $this->assertSame(
            ['cc' => '4242424242424242'],
            $filter(['cc' => '4242424242424242']),
            'Stripe standard credit card number test vector failed.'
        );
        $this->assertSame(
            ['cc' => '4242424242424242'],
            $filter(['cc' => '4242-4242-4242-4242']),
            'Hyphens. Hyphens, everywhere.'
        );
        $this->assertSame(
            ['cc' => '4242424242424242'],
            $filter(['cc' => '4242 4242 4242 4242']),
            'Atmosphere. Black holes. Astronauts. Nebulas. Jupiter. The Big Dipper.'
        );
    }
}
