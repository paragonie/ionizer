# Ionizer

[![Build Status](https://travis-ci.org/paragonie/ionizer.svg?branch=master)](https://travis-ci.org/paragonie/ionizer)
[![Latest Stable Version](https://poser.pugx.org/paragonie/ionizer/v/stable)](https://packagist.org/packages/paragonie/ionizer)
[![Latest Unstable Version](https://poser.pugx.org/paragonie/ionizer/v/unstable)](https://packagist.org/packages/paragonie/ionizer)
[![License](https://poser.pugx.org/paragonie/ionizer/license)](https://packagist.org/packages/paragonie/ionizer)
[![Downloads](https://img.shields.io/packagist/dt/paragonie/ionizer.svg)](https://packagist.org/packages/paragonie/ionizer)

Input filtering system used in [CMS Airship](https://github.com/paragonie/airship), now available
for use in any project. **Requires PHP 7 or higher.**

## What is Ionizer?

Ionizer is a structured input filtering system ideal for HTTP form data.

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
    WhiteList
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
        new WhiteList('US-1', 'US-2', 'EU-1', 'EU-2')
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
