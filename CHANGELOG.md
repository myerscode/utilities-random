# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Calendar Versioning](https://calver.org/).

## Unreleased

### Added
- Constraint system with pool filters and output constraints
- Built-in constraints: MustContainDigit, MustContainLetter, MustContainUppercase, NoRepeatingCharacters, NoSequentialCharacters, RegexConstraint, ExcludeCharacters, ExcludeSimilarCharacters
- Early conflict detection via canBeSatisfiedBy on output constraints
- New drivers: BinaryDriver, HexDriver, LowercaseAlphaDriver, UppercaseAlphaDriver, CustomDriver
- Benchmark script for performance regression testing
- Security audit workflow and Dependabot configuration
- CHANGELOG.md

### Changed
- Require PHP ^8.5
- Optimise string generation with batched random_bytes and rejection sampling
- Standardise CI workflows (tests, codecov, security audit)
- Standardise dev tooling (Pint PSR-12, PHPStan level max, Rector prepared sets)
- Achieve 100% test coverage

### Removed
- Legacy php.yml workflow (replaced by tests.yml)
