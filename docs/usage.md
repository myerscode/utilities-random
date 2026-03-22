# Utility

The `Utility` class is the main entry point for generating random strings. It wraps a driver and generator with a fluent API.

## Creating an Instance

Pass a [driver](drivers.md) class name or instance:

```php
use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Utility;

$utility = new Utility(AlphaNumericDriver::class);

// or with an instance
$utility = new Utility(new AlphaNumericDriver());
```

## Generating Values

```php
// Default length of 5
$random = $utility->generate();

// Custom length
$random = $utility->length(10)->generate();

// Chunked output with a spacer
$random = $utility->length(4)->chunks(3)->spacer('-')->generate();
// e.g. "aB3x-Kp9z-mN2q"
```

## Unique Values

Generate values that won't repeat within the utility's lifetime (or until `reset()` is called):

```php
$first  = $utility->length(10)->unique();
$second = $utility->length(10)->unique(); // guaranteed different from $first
```

Throws `UniqueThresholdReachedException` if it can't produce a unique value after 10 attempts.

## Applying Constraints

Constrain generation with [constraints](constraints.md):

```php
use Myerscode\Utilities\Random\Constraints\Pool\ExcludeSimilarCharacters;
use Myerscode\Utilities\Random\Constraints\Output\NoRepeatingCharacters;

$random = $utility
    ->constraints([ExcludeSimilarCharacters::class, NoRepeatingCharacters::class])
    ->length(10)
    ->generate();
```

## Other Methods

| Method | Description |
|--------|-------------|
| `seed()` | Re-seed the driver to regenerate the character pool |
| `collisions()` | Number of collisions encountered during `unique()` calls |
| `reset()` | Clear generated history and collision count |
| `permutations()` | Total possible permutations for the current pool and length |
