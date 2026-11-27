# Random Utilities

> A fluent PHP utility class to help create random values.

> ⚠ Characters are sampled with PHP's `random_bytes()` (the OS CSPRNG) using rejection sampling, so selection from the pool is unbiased. Duplicate pool entries and output constraints change the distribution (see [Usage](docs/usage.md#randomness)). The package has not been independently audited; assess it against your own security requirements before using it for passwords or tokens.

[![Latest Stable Version](https://poser.pugx.org/myerscode/utilities-random/v/stable)](https://packagist.org/packages/myerscode/utilities-random)
[![Total Downloads](https://poser.pugx.org/myerscode/utilities-random/downloads)](https://packagist.org/packages/myerscode/utilities-random)
[![PHP Version Require](http://poser.pugx.org/myerscode/utilities-random/require/php)](https://packagist.org/packages/myerscode/utilities-random)
[![License](https://poser.pugx.org/myerscode/utilities-random/license)](https://github.com/myerscode/utilities-random/blob/main/LICENSE)
[![Tests](https://github.com/myerscode/utilities-random/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/myerscode/utilities-random/actions/workflows/tests.yml)
[![codecov](https://codecov.io/gh/myerscode/utilities-random/graph/badge.svg)](https://codecov.io/gh/myerscode/utilities-random)

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
