<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

/**
 * Note: duplicate entries are kept, not removed. An entry listed twice is twice as
 * likely to be sampled, so pass a unique set if you want uniform output.
 */
class CustomDriver extends AbstractDriver implements RandomDriverInterface
{
    /** @var array<int, string> */
    private array $units = [];

    /**
     * @param  array<int, string>  $characters  The entries to use. Each entry is one unit, so
     *                                          multibyte and multi-character entries are never split.
     */
    public function __construct(private readonly array $characters)
    {
        parent::__construct();
    }

    public function seed(): void
    {
        $units = [];

        for ($i = 0; $i < 5; $i++) {
            foreach ($this->characters as $character) {
                $units[] = (string) $character;
            }
        }

        // Fisher-Yates on whole entries; byte-level shuffles would corrupt multibyte entries.
        for ($i = count($units) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$units[$i], $units[$j]] = [$units[$j], $units[$i]];
        }

        $this->units = $units;
        $this->digest = implode('', $units);
    }

    /**
     * The shuffled pool, one element per entry.
     *
     * @return array<int, string>
     */
    public function units(): array
    {
        return $this->units;
    }
}
