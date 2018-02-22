<?php

use ParagonIE\Ionizer\Filter\Special\{
    CreditCardNumberFilter,
    DateTimeFilter,
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
    public function testDateTimeFilter()
    {
        $filter = (new GeneralFilterContainer())
            ->addFilter(
                'dob', new DateTimeFilter(
                    'm/d/Y',
                    new DateTimeZone('Etc/GMT')
                )
            )->addFilter(
                'published',
                new DateTimeFilter(
                    DateTime::ATOM,
                    new DateTimeZone('Etc/GMT')
                )
            )->addFilter(
                'chicago',
                new DateTimeFilter(
                    DateTime::ATOM,
                    new DateTimeZone('America/Chicago')
                )
            )->addFilter(
                'london',
                new DateTimeFilter(
                    DateTime::ATOM,
                    new DateTimeZone('Europe/London')
                )
            )->addFilter(
                'newyork',
                new DateTimeFilter(
                    DateTime::ATOM,
                    new DateTimeZone('America/New_York')
                )
            );

        if (!($filter instanceof GeneralFilterContainer)) {
            $this->fail('Type error');
        }
        $testCases = $this->getDateTimeTestCases();
        foreach ($testCases as $index => $tc) {
            list($before, $after) = $tc;
            $this->assertEquals($after, $filter($before), $index);
        }
    }

    /**
     * @return array
     */
    private function getDateTimeTestCases(): array
    {
        return [
            [
                [
                    'chicago' => '1970-01-01',
                    'dob' => '1970-01-01',
                    'london' => '1970-01-01',
                    'newyork' => '1970-01-01',
                    'published' => '1970-01-01'
                ],
                [
                    'chicago' => '1970-01-01T00:00:00-06:00',
                    'dob' => '01/01/1970',
                    'london' => '1970-01-01T00:00:00+01:00',
                    'newyork' => '1970-01-01T00:00:00-05:00',
                    'published' => '1970-01-01T00:00:00+00:00'
                ]
            ], [
                [
                    'chicago' => '12/25/2017',
                    'dob' => '12/25/2017',
                    'london' => '12/25/2017',
                    'newyork' => '12/25/2017',
                    'published' => '12/25/2017'
                ],
                [
                    'chicago' => '2017-12-25T00:00:00-06:00',
                    'dob' => '12/25/2017',
                    'london' => '2017-12-25T00:00:00+00:00',
                    'newyork' => '2017-12-25T00:00:00-05:00',
                    'published' => '2017-12-25T00:00:00+00:00'
                ]
            ], [
                [
                    'chicago' => '1991-02-29',
                    'dob' => '1991-02-29',
                    'london' => '1991-02-29',
                    'newyork' => '1991-02-29',
                    'published' => '1991-02-29'
                ],
                [
                    'chicago' => '1991-03-01T00:00:00-06:00',
                    'dob' => '03/01/1991',
                    'london' => '1991-03-01T00:00:00+00:00',
                    'newyork' => '1991-03-01T00:00:00-05:00',
                    'published' => '1991-03-01T00:00:00+00:00'
                ]
            ], [
                [
                    'chicago' => '1992-02-29',
                    'dob' => '1992-02-29',
                    'london' => '1992-02-29',
                    'newyork' => '1992-02-29',
                    'published' => '1992-02-29'
                ],
                [
                    'chicago' => '1992-02-29T00:00:00-06:00',
                    'dob' => '02/29/1992',
                    'london' => '1992-02-29T00:00:00+00:00',
                    'newyork' => '1992-02-29T00:00:00-05:00',
                    'published' => '1992-02-29T00:00:00+00:00'
                ]
            ], [
                [
                    'chicago' => '12/25/2017 11:33 AM',
                    'dob' => '12/25/2017 11:33 AM',
                    'london' => '12/25/2017 11:33 AM',
                    'newyork' => '12/25/2017 11:33 AM',
                    'published' => '12/25/2017 11:33 AM'
                ],
                [
                    'chicago' => '2017-12-25T11:33:00-06:00',
                    'dob' => '12/25/2017',
                    'london' => '2017-12-25T11:33:00+00:00',
                    'newyork' => '2017-12-25T11:33:00-05:00',
                    'published' => '2017-12-25T11:33:00+00:00'
                ]
            ]
        ];
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
