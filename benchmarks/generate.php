<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Myerscode\Utilities\Random\Constraints\Output\MustContainDigit;
use Myerscode\Utilities\Random\Constraints\Output\MustContainUppercase;
use Myerscode\Utilities\Random\Constraints\Output\NoRepeatingCharacters;
use Myerscode\Utilities\Random\Constraints\Pool\ExcludeSimilarCharacters;
use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Generator;

$iterations = 1_000_000;

$scenarios = [
    'Plain (length 8)' => static function () use ($iterations): void {
        $generator = new Generator(new AlphaNumericDriver());
        for ($i = 0; $i < $iterations; $i++) {
            $generator->make(8);
        }
    },
    'Plain (length 32)' => static function () use ($iterations): void {
        $generator = new Generator(new AlphaNumericDriver());
        for ($i = 0; $i < $iterations; $i++) {
            $generator->make(32);
        }
    },
    'Plain (length 128)' => static function () use ($iterations): void {
        $generator = new Generator(new AlphaNumericDriver());
        for ($i = 0; $i < $iterations; $i++) {
            $generator->make(128);
        }
    },
    'Chunked (4x3 with spacer)' => static function () use ($iterations): void {
        $generator = new Generator(new AlphaNumericDriver());
        for ($i = 0; $i < $iterations; $i++) {
            $generator->make(4, 3, '-');
        }
    },
    'Pool constraint' => static function () use ($iterations): void {
        $generator = new Generator(new AlphaNumericDriver());
        $generator->setConstraints([new ExcludeSimilarCharacters()]);
        for ($i = 0; $i < $iterations; $i++) {
            $generator->make(8);
        }
    },
    'Output constraints' => static function () use ($iterations): void {
        $generator = new Generator(new AlphaNumericDriver());
        $generator->setConstraints([new MustContainDigit(), new MustContainUppercase()]);
        for ($i = 0; $i < $iterations; $i++) {
            $generator->make(8);
        }
    },
    'Combined constraints' => static function () use ($iterations): void {
        $generator = new Generator(new AlphaNumericDriver());
        $generator->setConstraints([
            new ExcludeSimilarCharacters(),
            new NoRepeatingCharacters(),
            new MustContainDigit(),
            new MustContainUppercase(),
        ]);
        for ($i = 0; $i < $iterations; $i++) {
            $generator->make(12);
        }
    },
];

echo sprintf("Benchmark: %s iterations per scenario\n", number_format($iterations));
echo str_repeat('-', 55) . "\n";

foreach ($scenarios as $name => $fn) {
    $start = hrtime(true);
    $fn();
    $elapsed = (hrtime(true) - $start) / 1_000_000_000;
    $perOp = $elapsed / $iterations;

    echo sprintf("%-30s %8.2f s  (%0.6f s/op)\n", $name, $elapsed, $perOp);
}

echo str_repeat('-', 55) . "\n";
