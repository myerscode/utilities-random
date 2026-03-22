# Drivers

Drivers define the character pool used for random string generation. Each driver produces a shuffled digest of characters that the generator picks from.

## Built-in Drivers

| Driver | Characters | Pool Size |
|--------|-----------|-----------|
| `AlphaDriver` | Uppercase and lowercase letters (`a-z`, `A-Z`) | 52 |
| `AlphaNumericDriver` | Letters and digits (`a-z`, `A-Z`, `0-9`) | 62 |
| `BinaryDriver` | Binary digits (`0`, `1`) | 50 |
| `HexDriver` | Hexadecimal characters (`0-9`, `a-f`) | 16 |
| `LowercaseAlphaDriver` | Lowercase letters only (`a-z`) | 52 |
| `NumericDriver` | Digits only (`0-9`) | 50 |
| `UppercaseAlphaDriver` | Uppercase letters only (`A-Z`) | 52 |

## Usage

Pass a driver class name or instance when creating a `Utility`:

```php
use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Drivers\HexDriver;
use Myerscode\Utilities\Random\Utility;

$alphaNumeric = new Utility(AlphaNumericDriver::class);
$hex = new Utility(new HexDriver());
```

## CustomDriver

For full control over the character set, use `CustomDriver` with your own characters:

```php
use Myerscode\Utilities\Random\Drivers\CustomDriver;
use Myerscode\Utilities\Random\Utility;

// Emoji pool
$driver = new CustomDriver(['🎲', '🎯', '🎰', '🃏']);
$utility = new Utility($driver);
$result = $utility->length(5)->generate();

// Special characters
$driver = new CustomDriver(['!', '@', '#', '$', '%']);
$utility = new Utility($driver);
$result = $utility->length(8)->generate();
```

Note: since `CustomDriver` requires constructor arguments, pass an instance rather than a class name.

## Creating Your Own Driver

Implement `RandomDriverInterface` (or extend `AbstractDriver` for the shuffle helpers):

```php
use Myerscode\Utilities\Random\Drivers\AbstractDriver;
use Myerscode\Utilities\Random\Drivers\RandomDriverInterface;

class OctalDriver extends AbstractDriver implements RandomDriverInterface
{
    public function seed(): void
    {
        $ranges = [];

        for ($i = 0; $i < 5; $i++) {
            $ranges[] = implode('', array_map(strval(...), $this->shuffleArray(range(0, 7))));
        }

        $seed = $this->shuffleString(str_shuffle(implode('', $ranges)));

        for ($i = 0; $i < $this->iterations; $i++) {
            $seed = $this->shuffleString($seed);
        }

        $this->digest = $seed;
    }
}
```

Then use it like any other driver:

```php
$utility = new Utility(OctalDriver::class);
$octal = $utility->length(8)->generate(); // e.g. "37150462"
```
