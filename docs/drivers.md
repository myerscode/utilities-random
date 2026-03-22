# Drivers

Drivers define the character pool used for random string generation. Each driver produces a shuffled digest of characters that the generator picks from.

## Built-in Drivers

| Driver | Characters | Pool Size |
|--------|-----------|-----------|
| `AlphaDriver` | Uppercase and lowercase letters (`a-z`, `A-Z`) | 52 |
| `AlphaNumericDriver` | Letters and digits (`a-z`, `A-Z`, `0-9`) | 62 |
| `NumericDriver` | Digits only (`0-9`, repeated for pool depth) | 50 |

## Usage

Pass a driver class name or instance when creating a `Utility`:

```php
use Myerscode\Utilities\Random\Drivers\AlphaDriver;
use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Drivers\NumericDriver;
use Myerscode\Utilities\Random\Utility;

$alpha = new Utility(AlphaDriver::class);
$alphaNumeric = new Utility(new AlphaNumericDriver());
$numeric = new Utility(NumericDriver::class);
```

## Creating a Custom Driver

Implement `RandomDriverInterface` (or extend `AbstractDriver` for the shuffle helpers):

```php
use Myerscode\Utilities\Random\Drivers\AbstractDriver;
use Myerscode\Utilities\Random\Drivers\RandomDriverInterface;

class HexDriver extends AbstractDriver implements RandomDriverInterface
{
    public function seed(): void
    {
        $chars = array_merge(range(0, 9), range('a', 'f'));

        $seed = $this->shuffleString(implode('', $chars));

        for ($i = 0; $i < $this->iterations; $i++) {
            $seed = $this->shuffleString($seed);
        }

        $this->digest = $seed;
    }
}
```

Then use it like any other driver:

```php
$utility = new Utility(HexDriver::class);
$hex = $utility->length(8)->generate(); // e.g. "3fa8c1b0"
```
