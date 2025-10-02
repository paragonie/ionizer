# Ionizer

[![Build Status](https://github.com/paragonie/ionizer/actions/workflows/ci.yml/badge.svg)](https://github.com/paragonie/ionizer/actions)
[![Psalm Status](https://github.com/paragonie/ionizer/actions/workflows/psalm.yml/badge.svg)](https://github.com/paragonie/ionizere/actions)
[![Latest Stable Version](https://poser.pugx.org/paragonie/ionizer/v/stable)](https://packagist.org/packages/paragonie/ionizer)
[![Latest Unstable Version](https://poser.pugx.org/paragonie/ionizer/v/unstable)](https://packagist.org/packages/paragonie/ionizer)
[![License](https://poser.pugx.org/paragonie/ionizer/license)](https://packagist.org/packages/paragonie/ionizer)
[![Downloads](https://img.shields.io/packagist/dt/paragonie/ionizer.svg)](https://packagist.org/packages/paragonie/ionizer)

Ionizer provides strict typing and input validation for dynamic inputs (i.e. HTTP request parameters).
**Requires PHP 8.1 or higher.** 

For PHP 7 and 8.0 support, please refer to the [v1.x](https://github.com/paragonie/ionizer/tree/v1.x) branch.

## What is Ionizer?

Ionizer is a structured input filtering system ideal for HTTP form data.

### Why is Ionizer important?

Aside from the benefits of being able to strictly type your applications that accept user input,
Ionizer makes it easy to mitigate [some NoSQL injection techniques](https://www.php.net/manual/en/mongodb.security.request_injection.php).
Learn more about [preventing NoSQL injection with Ionizer](docs/nosql-injection-prevention.md).

## Installing

Get Composer, then run the following:

```terminal
composer require paragonie/ionizer
```

## Usage

```php
<?php

use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\{
    StringFilter,
    AllowList
};

// Define properties to filter:
$ic = new GeneralFilterContainer();
$ic->addFilter(
        'username',
        (new StringFilter())->setPattern('^[A-Za-z0-9_\-]{3,24}$')
    )
    ->addFilter('passphrase', new StringFilter())
    ->addFilter(
        'domain',
        new AllowList('US-1', 'US-2', 'EU-1', 'EU-2')
    );

// Invoke the filter container on the array to get the filtered result:
try {
    // $post passed all of our filters.
    $post = $ic($_POST);
} catch (\TypeError $ex) {
    // Invalid data provided.
}
```

Ionizer can even specify structured input with some caveats.

```php
<?php

use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\{
    IntFilter,
    IntArrayFilter,
    StringArrayFilter,
    StringFilter
};

$ic = new GeneralFilterContainer();
    // You can type entire arrays at once:
$ic->addFilter('numbers', new IntArrayFilter())
    ->addFilter('strings', new StringArrayFilter())
    
    // You can also specify subkeys, separated by a period:
    ->addFilter('user.name', new StringFilter())
    ->addFilter('user.unixtime', new IntFilter());

$input = [
    'numbers' => [1, 2, 3],
    'strings' => ['a', 'b'],
    'user' => [
        'name' => 'test',
        'unixtime' => time()
    ]    
];

try {
    $valid = $ic($input);
} catch (\TypeError $ex) {
}
```

## Support Contracts

If your company uses this library in their products or services, you may be
interested in [purchasing a support contract from Paragon Initiative Enterprises](https://paragonie.com/enterprise).
