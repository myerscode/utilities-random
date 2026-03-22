# Constraints

Constraints add restrictions to the generation process. Pass them as class names or instances via the `constraints()` method:

```php
use Myerscode\Utilities\Random\Constraints\Pool\ExcludeSimilarCharacters;
use Myerscode\Utilities\Random\Constraints\Output\NoRepeatingCharacters;

$random = $utility
    ->constraints([ExcludeSimilarCharacters::class, NoRepeatingCharacters::class])
    ->length(10)
    ->generate();
```

Constraints with configurable options (like `MustContainDigit`, `NoSequentialCharacters`) use sensible defaults when passed as class names. To customise them, pass instances instead:

```php
use Myerscode\Utilities\Random\Constraints\Output\MustContainDigit;

// Class name — uses default minimum of 1
$utility->constraints([MustContainDigit::class]);

// Instance — custom minimum of 3
$utility->constraints([new MustContainDigit(3)]);
```

There are two categories of constraints:

## Pool Constraints

Pool constraints filter the character pool before generation — characters removed from the pool will never appear in the output.

Implement `PoolConstraint` to create your own:

```php
use Myerscode\Utilities\Random\Constraints\PoolConstraint;

class ExcludeVowels implements PoolConstraint
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

## Output Constraints

Output constraints check the generated string after creation and reject it if it doesn't meet criteria. The generator will retry until a valid string is produced (up to 100 attempts).

Implement `OutputConstraint` to create your own:

```php
use Myerscode\Utilities\Random\Constraints\OutputConstraint;

class NoPalindromes implements OutputConstraint
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

The generator checks whether output constraints can be satisfied by the current character pool before it starts generating. This prevents wasting time on impossible combinations.

For example, using `NumericDriver` with `MustContainLetter` will throw an `UnsatisfiableConstraintException` immediately rather than silently failing after 100 retries:

```php
use Myerscode\Utilities\Random\Drivers\NumericDriver;
use Myerscode\Utilities\Random\Utility;
use Myerscode\Utilities\Random\Constraints\Output\MustContainLetter;

$utility = new Utility(NumericDriver::class);
$utility->constraints([MustContainLetter::class])->length(10)->generate();
// throws UnsatisfiableConstraintException
```

Similarly, if pool constraints remove all characters from the pool, an `EmptyPoolException` is thrown at constraint setup time:

```php
use Myerscode\Utilities\Random\Constraints\Pool\ExcludeCharacters;

$utility = new Utility(NumericDriver::class);
$utility->constraints([new ExcludeCharacters(['0','1','2','3','4','5','6','7','8','9'])]);
// throws EmptyPoolException
```

The `canBeSatisfiedBy` check also considers the requested output length. For instance, `NoRepeatingCharacters` with a single-character pool is fine for length 1, but impossible for length 2+.

When creating custom output constraints, implement `canBeSatisfiedBy(string $pool, int $length): bool` to opt into this check. Return `true` if you can't predict satisfiability from the pool alone.

## Built-in Constraints

| Constraint | Type | Description |
|------------|------|-------------|
| `Pool\ExcludeCharacters` | Pool | Removes a configurable set of characters from the pool |
| `Pool\ExcludeSimilarCharacters` | Pool | Removes visually similar characters (`o`, `O`, `0`, `I`, `1`, `l`) |
| `Output\MustContainDigit` | Output | Requires at least X digits (default 1) |
| `Output\MustContainLetter` | Output | Requires at least X letters (default 1) |
| `Output\MustContainUppercase` | Output | Requires at least X uppercase letters (default 1) |
| `Output\NoRepeatingCharacters` | Output | Rejects strings with consecutive duplicate characters |
| `Output\NoSequentialCharacters` | Output | Rejects strings with sequential runs like `abc` or `321` (configurable length, default 3) |
| `Output\RegexConstraint` | Output | Validates output against a user-provided regex pattern |

## Examples

### Exclude specific characters

```php
use Myerscode\Utilities\Random\Constraints\Pool\ExcludeCharacters;

// Remove specific characters from the pool
$random = $utility
    ->constraints([new ExcludeCharacters(['@', '#', '$'])])
    ->generate();
```

### Enforce password-like requirements

```php
use Myerscode\Utilities\Random\Constraints\Output\MustContainDigit;
use Myerscode\Utilities\Random\Constraints\Output\MustContainUppercase;
use Myerscode\Utilities\Random\Constraints\Output\MustContainLetter;

// At least 2 digits, 1 uppercase, and 1 letter
$random = $utility
    ->constraints([
        new MustContainDigit(2),
        new MustContainUppercase(),
        new MustContainLetter(),
    ])
    ->length(12)
    ->generate();
```

### Prevent sequential runs

```php
use Myerscode\Utilities\Random\Constraints\Output\NoSequentialCharacters;

// Reject sequences of 3+ (default)
$random = $utility
    ->constraints([new NoSequentialCharacters()])
    ->generate();

// Stricter — reject sequences of 2+
$random = $utility
    ->constraints([new NoSequentialCharacters(2)])
    ->generate();
```

### Custom regex validation

```php
use Myerscode\Utilities\Random\Constraints\Output\RegexConstraint;

// Must start with a letter
$random = $utility
    ->constraints([new RegexConstraint('/^[a-zA-Z]/')])
    ->generate();
```
