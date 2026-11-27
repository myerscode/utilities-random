# Drivers

Drivers define the character pool used for random string generation. Each driver produces a digest: the characters the generator picks from. Pool order does not affect randomness, because the generator samples positions with `random_bytes()`. Only which characters are in the pool, and how often, matters. Pools of any size are supported (the generator reads 1, 2 or 4 bytes per sample as needed).

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

Each entry is one unit: multibyte characters (like the emoji above) and multi-character entries such as `'ab'` are sampled whole and never split.

Duplicate entries are kept, not removed. An entry listed twice is twice as likely to be sampled, so pass a unique set if you want uniform output.

Note: since `CustomDriver` requires constructor arguments, pass an instance rather than a class name.

## Creating Your Own Driver

Implement `RandomDriverInterface` (or extend `AbstractDriver`, which stores the digest for you). `seed()` only needs to set `$this->digest` to the characters you want in the pool; no shuffling is required:

```php
use Myerscode\Utilities\Random\Drivers\AbstractDriver;
use Myerscode\Utilities\Random\Drivers\RandomDriverInterface;

class OctalDriver extends AbstractDriver implements RandomDriverInterface
{
    public function seed(): void
    {
        $this->digest = implode('', range(0, 7));
    }
}
```

Then use it like any other driver:

```php
$utility = new Utility(OctalDriver::class);
$octal = $utility->length(8)->generate(); // e.g. "37150462"
```
