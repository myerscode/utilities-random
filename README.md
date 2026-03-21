# Random Utilities

> A fluent PHP utility class to help create random values.

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

## Available Drivers

- `AlphaDriver` — uppercase and lowercase letters
- `AlphaNumericDriver` — letters and digits
- `NumericDriver` — digits only

## License

MIT
