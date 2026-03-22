# Rules

Rules add constraints to the generation process. Pass them as class names or instances via the `rules()` method:

```php
use Myerscode\Utilities\Random\Rules\ExcludeSimilarCharacters;
use Myerscode\Utilities\Random\Rules\NoRepeatingCharacters;

$random = $utility
    ->rules([ExcludeSimilarCharacters::class, NoRepeatingCharacters::class])
    ->length(10)
    ->generate();
```

Rules with configurable options (like `MustContainDigit`, `NoSequentialCharacters`) use sensible defaults when passed as class names. To customise them, pass instances instead:

```php
use Myerscode\Utilities\Random\Rules\MustContainDigit;

// Class name — uses default minimum of 1
$utility->rules([MustContainDigit::class]);

// Instance — custom minimum of 3
$utility->rules([new MustContainDigit(3)]);
```

There are two categories of rules:

## Pool Rules

Pool rules filter the character pool before generation — characters removed from the pool will never appear in the output.

Implement `PoolRule` to create your own:

```php
use Myerscode\Utilities\Random\Rules\PoolRule;

class ExcludeVowels implements PoolRule
{
    public function filter(string $pool): string
    {
        return implode('', array_filter(
            str_split($pool),
            fn (string $char): bool => !in_array(strtolower($char), ['a', 'e', 'i', 'o', 'u'], true),
        ));
    }
}
```

## Validation Rules

Validation rules check the generated string after creation and reject it if it doesn't meet criteria. The generator will retry until a valid string is produced (up to 100 attempts).

Implement `ValidationRule` to create your own:

```php
use Myerscode\Utilities\Random\Rules\ValidationRule;

class NoPalindromes implements ValidationRule
{
    public function passes(string $value): bool
    {
        return $value !== strrev($value);
    }

    public function canBeSatisfiedBy(string $pool, int $length): bool
    {
        // Can't predict palindromes from the pool alone
        return true;
    }
}
```

## Early Conflict Detection

The generator checks whether validation rules can be satisfied by the current character pool before it starts generating. This prevents wasting time on impossible combinations.

For example, using `NumericDriver` with `MustContainLetter` will throw an `UnsatisfiableRuleException` immediately rather than silently failing after 100 retries:

```php
use Myerscode\Utilities\Random\Drivers\NumericDriver;
use Myerscode\Utilities\Random\Utility;
use Myerscode\Utilities\Random\Rules\MustContainLetter;

$utility = new Utility(NumericDriver::class);
$utility->rules([MustContainLetter::class])->length(10)->generate();
// throws UnsatisfiableRuleException
```

Similarly, if pool rules remove all characters from the pool, an `EmptyPoolException` is thrown at rule setup time:

```php
use Myerscode\Utilities\Random\Rules\ExcludeCharacters;

$utility = new Utility(NumericDriver::class);
$utility->rules([new ExcludeCharacters(['0','1','2','3','4','5','6','7','8','9'])]);
// throws EmptyPoolException
```

The `canBeSatisfiedBy` check also considers the requested output length. For instance, `NoRepeatingCharacters` with a single-character pool is fine for length 1, but impossible for length 2+.

When creating custom validation rules, implement `canBeSatisfiedBy(string $pool, int $length): bool` to opt into this check. Return `true` if you can't predict satisfiability from the pool alone.

## Built-in Rules

| Rule | Type | Description |
|------|------|-------------|
| `ExcludeCharacters` | Pool | Removes a configurable set of characters from the pool |
| `ExcludeSimilarCharacters` | Pool | Removes visually similar characters (`o`, `O`, `0`, `I`, `1`, `l`) |
| `MustContainDigit` | Validation | Requires at least X digits (default 1) |
| `MustContainLetter` | Validation | Requires at least X letters (default 1) |
| `MustContainUppercase` | Validation | Requires at least X uppercase letters (default 1) |
| `NoRepeatingCharacters` | Validation | Rejects strings with consecutive duplicate characters |
| `NoSequentialCharacters` | Validation | Rejects strings with sequential runs like `abc` or `321` (configurable length, default 3) |
| `RegexRule` | Validation | Validates output against a user-provided regex pattern |

## Examples

### Exclude specific characters

```php
use Myerscode\Utilities\Random\Rules\ExcludeCharacters;

// Remove specific characters from the pool
$random = $utility
    ->rules([new ExcludeCharacters(['@', '#', '$'])])
    ->generate();
```

### Enforce password-like requirements

```php
use Myerscode\Utilities\Random\Rules\MustContainDigit;
use Myerscode\Utilities\Random\Rules\MustContainUppercase;
use Myerscode\Utilities\Random\Rules\MustContainLetter;

// At least 2 digits, 1 uppercase, and 1 letter
$random = $utility
    ->rules([
        new MustContainDigit(2),
        new MustContainUppercase(),
        new MustContainLetter(),
    ])
    ->length(12)
    ->generate();
```

### Prevent sequential runs

```php
use Myerscode\Utilities\Random\Rules\NoSequentialCharacters;

// Reject sequences of 3+ (default)
$random = $utility
    ->rules([new NoSequentialCharacters()])
    ->generate();

// Stricter — reject sequences of 2+
$random = $utility
    ->rules([new NoSequentialCharacters(2)])
    ->generate();
```

### Custom regex validation

```php
use Myerscode\Utilities\Random\Rules\RegexRule;

// Must start with a letter
$random = $utility
    ->rules([new RegexRule('/^[a-zA-Z]/')])
    ->generate();
```
