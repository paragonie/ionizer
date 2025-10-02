# Ionizer Developer Documentation

This document provides detailed examples for the different input filters available in Ionizer.

**Other Documents**

* [Preventing NoSQL Injection with Ionizer](nosql-injection-prevention.md)

**Contents of This Document**

* [Scalar Type Filters](#scalar-type-filters)
   * [`BoolFilter`](#boolfilter)
   * [`FloatFilter`](#floatfilter)
   * [`IntFilter`](#intfilter)
   * [`StringFilter`](#stringfilter)
* [Array Filters](#array-filters)
   * [`BoolArrayFilter`](#boolarrayfilter)
   * [`FloatArrayFilter`](#floatarrayfilter)
   * [`IntArrayFilter`](#intarrayfilter)
   * [`StringArrayFilter`](#stringarrayfilter)
* [Other Filters](#other-filters)
   * [`AllowList`](#allowlist)
   * [`EmailAddressFilter`](#emailaddressfilter)

## Scalar Type Filters

Below is the documentation for scalar type filters available in Ionizer.


### `BoolFilter`

The `BoolFilter` validates a single boolean value.

**Example:**
```php
<?php
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\BoolFilter;

$ic = new GeneralFilterContainer();
$ic->addFilter('is_active', new BoolFilter());

$input = ['is_active' => true];

try {
    $valid = $ic($input);
} catch (\TypeError $ex) {
    // Handle error
}
```

### `FloatFilter`

The `FloatFilter` validates a single float value.

**Example:**
```php
<?php
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\FloatFilter;

$ic = new GeneralFilterContainer();
$ic->addFilter('price', new FloatFilter());

$input = ['price' => 123.45];

try {
    $valid = $ic($input);
} catch (\TypeError $ex) {
    // Handle error
}
```

### `IntFilter`

The `IntFilter` validates a single integer value.

**Example:**
```php
<?php
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\IntFilter;

$ic = new GeneralFilterContainer();
$ic->addFilter('user_id', new IntFilter());

$input = ['user_id' => 12345];

try {
    $valid = $ic($input);
} catch (\TypeError $ex) {
    // Handle error
}
```

### `StringFilter`

The `StringFilter` is used to validate a string. You can set a regex pattern to validate against.

**Example:**
```php
<?php
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\StringFilter;

$ic = new GeneralFilterContainer();
$ic->addFilter(
    'username',
    (new StringFilter())->setPattern('^[A-Za-z0-9_\-]{3,24}$')
);

$input = ['username' => 'my_valid_username'];
$invalid = ['username' => 'invalid-username!'];

try {
    $valid = $ic($input); // OK
    $ic($invalid); // Throws TypeError
} catch (\TypeError $ex) {
    // Handle error
}
```

## Array Filters

Sometimes you need to accept a list of values, rather than a single value. These input filters allow you to limit the
inputs to a flat, one-dimensional array consisting of specific values.

### `BoolArrayFilter`

The `BoolArrayFilter` is used to ensure that the input is a one-dimensional array of booleans. It will cast any
non-empty value to `true` and empty values to `false`.

```php
<?php
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\BoolArrayFilter;

$ic = new GeneralFilterContainer();
$ic->addFilter('options', new BoolArrayFilter());

$input = [
    'options' => [true, false, 1, 0, 'true', 'false', '', null]
];

try {
    $valid = $ic($input);
    /*
     $valid will be:
     [
         'options' => [true, false, true, false, true, false, false, false]
     ]
    */
} catch (\TypeError $ex) {
    // Handle error
}
```

### `FloatArrayFilter`

The `FloatArrayFilter` ensures the input is a one-dimensional array of floats.

**Example:**
```php
<?php
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\FloatArrayFilter;

$ic = new GeneralFilterContainer();
$ic->addFilter('prices', new FloatArrayFilter());

$input = ['prices' => [9.99, 19.99, 0.99]];

try {
    $valid = $ic($input);
} catch (\TypeError $ex) {
    // Handle error
}
```

### `IntArrayFilter`

The `IntArrayFilter` is used to ensure that the input is a one-dimensional array of integers. It attempts to cast values
to integers.

* Numeric strings will be cast to integers.
* Floats will be cast to integers (truncating the decimal part).
* `null` or empty strings (`''`) will be replaced with the default value, which is `0`.
* Non-numeric strings will cause a `TypeError`.

```php
<?php
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\IntArrayFilter;

$ic = new GeneralFilterContainer();
$ic->addFilter('numbers', new IntArrayFilter());

// Valid input
$input = [
    'numbers' => [1, '2', 3.0, null, '']
];

try {
    $valid = $ic($input);
    /*
     $valid will be:
     [
         'numbers' => [1, 2, 3, 0, 0]
     ]
    */
} catch (\TypeError $ex) {
    // Handle error
}

// Invalid input
$invalidInput = [
    'numbers' => [1, 'foo', 3]
];

try {
    $ic($invalidInput);
} catch (\TypeError $ex) {
    // This will throw a TypeError because 'foo' is not a valid integer.
}
```

### `StringArrayFilter`

The `StringArrayFilter` ensures the input is a one-dimensional array of strings.

**Example:**
```php
<?php
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\StringArrayFilter;

$ic = new GeneralFilterContainer();
$ic->addFilter('tags', new StringArrayFilter());

$input = ['tags' => ['php', 'security', 'ionizer']];

try {
    $valid = $ic($input);
} catch (\TypeError $ex) {
    // Handle error
}
```

## Other Filters

### `AllowList`

The `AllowList` filter ensures that the input value is one of a predefined set of allowed values.

**Example:**
```php
<?php
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\AllowList;

$ic = new GeneralFilterContainer();
$ic->addFilter(
    'domain',
    new AllowList('US-1', 'US-2', 'EU-1', 'EU-2')
);

$input = ['domain' => 'US-1'];
$invalid = ['domain' => 'CA-1'];

try {
    $valid = $ic($input); // OK
    $ic($invalid); // Throws TypeError
} catch (\TypeError $ex) {
    // Handle error
}
```

### `EmailAddressFilter`

The `EmailAddressFilter` filter validates that the input is an email address for a valid domain name with an MX record.
This means that there is only one `@` character in the string and what follows is a valid email address for receiving
email. It doesn't guarantee that there is a valid inbox on the other end.

```php
<?php
use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\Special\EmailAddressFilter;

$ic = new GeneralFilterContainer();
$ic->addFilter(
    'email',
    new EmailAddressFilter()
);

$input = ['email' => 'foo@example.com'];
$invalid = ['email' => 'foo@invalid-domain-name-goes-here'];

try {
    $valid = $ic($input); // OK
    $ic($invalid); // Throws TypeError
} catch (\TypeError $ex) {
    // Handle error
}
```
