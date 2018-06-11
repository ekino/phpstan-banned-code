# PHPStan Banned Code

[![pipeline status](https://gitlab.ekino.com/php-labs/phpstan/phpstan-banned-code/badges/master/pipeline.svg)](https://gitlab.ekino.com/php-labs/phpstan/phpstan-banned-code/commits/master)
[![coverage report](https://gitlab.ekino.com/php-labs/phpstan/phpstan-banned-code/badges/master/coverage.svg)](https://gitlab.ekino.com/php-labs/phpstan/phpstan-banned-code/commits/master)

## Basic usage

To use this extension, require it using [Composer](https://getcomposer.org/):

```bash
composer require --dev phpstan/phpstan-banned-code
```

And include extension.neon in your project's PHPStan config:

```
includes:
	- vendor/phpstan/phpstan-banned-code/extension.neon
```

## Advanced usage

You can configure this library with parameters:

```
parameters:
	banned_code:
		eval: true          # enable detection of `eval`
		exit: true          # enable detection of `die/exit`
		functions:          # banned functions
			- dump
			- print_r
			- var_dump
```
