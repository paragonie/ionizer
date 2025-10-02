# Preventing NoSQL Injection with Ionizer

NoSQL databases like MongoDB are powerful, but they can be vulnerable to injection attacks if user input is not handled
carefully. This document explains how "request injection" attacks work in PHP with MongoDB and how to use Ionizer to 
prevent them.

## The Vulnerability: Request Injection

When building queries for MongoDB in PHP, it's common to use associative arrays. For example, to find a user by their
username, you might construct a query like this:

```php
<?php
$query = new \MongoDB\Driver\Query(['username' => $_GET['username']]);
```

> ![NOTE]
> Professional developers will not recklessly handle superglobals like this and expect to be secure, but it's a good,
> simplified example to work with. In practice, the avenues for setting up this attack are more subtle.

If a user visits `http://example.com?username=alice`, the query becomes `['username' => 'alice']`. All is well so far.

However, PHP has a feature where it can parse query string parameters with square brackets into nested arrays. An 
attacker can exploit this. For example, if they craft a URL like this:

`http://example.com?username[$ne]=foo`

PHP will parse `$_GET['username']` into `['$ne' => 'foo']`. Your MongoDB query then becomes:

```php
<?php
$query = new \MongoDB\Driver\Query(['username' => ['$ne' => 'foo']]);
```

This query will select all documents where the `username` is **not equal to** `foo`. This could potentially return all 
users in your database, leading to a data leak. This is a form of NoSQL injection.

## The Solution: Strict Input Validation with Ionizer

The best way to prevent this type of vulnerability is to strictly validate all user input before it's used in a database
query. You need to ensure that the data is of the expected type and format.

Ionizer is a library that makes this easy. It allows you to define a set of filters for your expected input. If the 
input doesn't match the filters, Ionizer will throw an exception, and you can reject the request.

### Example: Using Ionizer to Sanitize Query Parameters

Here's how you can use Ionizer to protect the example above:

```php
<?php

use ParagonIE\Ionizer\GeneralFilterContainer;
use ParagonIE\Ionizer\Filter\StringFilter;

// 1. Define your filters. We expect 'username' to be a string.
$filterContainer = new GeneralFilterContainer();
$filterContainer->addFilter(
    'username',
    // We can also add a regex pattern for the username format.
    (new StringFilter())->setPattern('^[A-Za-z0-9_\-]{3,24}$')
);

try {
    // 2. Process the input against the filters.
    // Ionizer will ensure $_GET contains a 'username' key, and its value is a string.
    // If $_GET['username'] is an array (like in the attack scenario),
    // a TypeError will be thrown.
    $filteredInput = $filterContainer($_GET);

    // 3. Use the sanitized input in your query.
    $query = new \MongoDB\Driver\Query(['username' => $filteredInput['username']]);
    
    // ... proceed to execute the query safely.

} catch (\TypeError $ex) {
    // 4. Handle invalid input.
    // The input did not match our filter rules.
    // Log the error and return an appropriate HTTP response (e.g., 400 Bad Request).
    header("HTTP/1.1 400 Bad Request");
    echo "Invalid input.";
    exit;
}
```

By using Ionizer to validate that `username` is a string, you prevent the attacker from injecting a malicious array
into your MongoDB query, effectively mitigating the request injection vulnerability.
