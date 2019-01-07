# PHPStan Banned Code

## Basic usage

To use this extension, require it using [Composer](https://getcomposer.org/):

```bash
composer require --dev ekino/phpstan-banned-code
```

And include extension.neon in your project's PHPStan config:

```
includes:
	- vendor/ekino/phpstan-banned-code/extension.neon
```

## Advanced usage

You can configure this library with parameters:

```
parameters:
	banned_code:
		eval: true              # enable detection of `eval`
		exit: true              # enable detection of `die/exit`
		echo: true              # enable detection of `echo`
		functions:              # banned functions
			- debug_backtrace
			- dump
			- exec
			- passthru
			- print_r
			- proc_open
			- shell_exec
			- system
			- var_dump
		use_from_tests: true    # enable detection of `use Tests\Foo\Bar` in a non-test file
```
