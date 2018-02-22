<?php

use ParagonIE\Ionizer\Filter\Special\{
    CreditCardNumberFilter,
    EmailAddressFilter
};
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\InvalidDataException;
use PHPUnit\Framework\TestCase;


/**
 * Class SpecialTest
 */
class SpecialTest extends TestCase
{
    /**
     * @throws Error
     * @throws InvalidDataException
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

    /**
     * @throws Error
     * @throws InvalidDataException
     */
    public function testEmailAddressFilter()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter('email', new EmailAddressFilter());

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }
        $this->assertSame(
            ['email' => 'test@localhost.us'],
            $filter(['email' => 'test@localhost.us'])
        );
        $valid = [
            'email@domain.com',
            'firstname.lastname@domain.com',
            'email@subdomain.domain.com',
            'firstname+lastname@domain.com',
            'email@[123.123.123.123]',
            '"email"@domain.com',
            '1234567890@domain.com',
            'email@paragonie.com',
            'email@pie-hosted.com',
            '_______@domain.com',
            'email@domain.name',
            'email@domain.co.jp',
            'firstname-lastname@domain.com'
        ];

        foreach ($valid as $in) {
            // Don't throw an exception
            $filter(['email' => $in]);
        }

        $invalid = [
            'plainaddress',
            '#@%^%#$@#$@#.com',
            '@domain.com',
            'email.domain.com',
            'email@domain@domain.com',
            '.email@domain.com',
            'email.@domain.com',
            'email..email@domain.com',
            'あいうえお@domain.com',
            'email@domain.com (Joe Smith)',
            'email@domain',
            'email@-domain.com',
            'email@domain.web',
            'email@111.222.333.44444',
            'email@domain..com'
        ];
        foreach ($invalid as $in) {
            try {
                $filter(['email' => $in]);
                $this->fail('Invalid email address accepted: ' . $in);
            } catch (InvalidDataException $ex) {
            }
        }
    }
}
