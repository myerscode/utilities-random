# Random Utilities

> A fluent PHP utility class to help create random values.

> ⚠️ This package is designed for generating random strings, codes, and identifiers — not for cryptographic purposes. Do not use it for passwords, tokens, or anything requiring cryptographic security.

[![Latest Stable Version](https://poser.pugx.org/myerscode/utilities-random/v/stable)](https://packagist.org/packages/myerscode/utilities-random)
[![Total Downloads](https://poser.pugx.org/myerscode/utilities-random/downloads)](https://packagist.org/packages/myerscode/utilities-random)
[![License](https://poser.pugx.org/myerscode/utilities-random/license)](https://packagist.org/packages/myerscode/utilities-random)
![Tests](https://github.com/myerscode/utilities-random/workflows/Tests/badge.svg?branch=main)

## Requirements

- PHP 8.5+

## Install

```shell
composer require myerscode/utilities-random
```

## Usage

```php
use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Utility;

$utility = new Utility(AlphaNumericDriver::class);

// Generate a random string of length 5
$random = $utility->generate();

// Generate with custom length
$random = $utility->length(10)->generate();

// Generate chunked values
$random = $utility->length(4)->chunks(3)->spacer('-')->generate();
// e.g. "aB3x-Kp9z-mN2q"

// Generate unique values
$unique = $utility->length(10)->unique();
```

## Documentation

- [Usage](docs/usage.md) — full API reference and usage examples
- [Drivers](docs/drivers.md) — built-in drivers and creating custom ones
- [Constraints](docs/constraints.md) — constraining generation with pool filters and output constraints

## License

MIT
