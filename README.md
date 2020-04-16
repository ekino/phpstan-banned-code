# PHPStan Banned Code

[![Latest Stable Version](https://poser.pugx.org/ekino/phpstan-banned-code/v/stable)](https://packagist.org/packages/ekino/phpstan-banned-code)
[![Build Status](https://travis-ci.org/ekino/phpstan-banned-code.svg?branch=master)](https://travis-ci.org/ekino/phpstan-banned-code)
[![Coverage Status](https://coveralls.io/repos/ekino/phpstan-banned-code/badge.svg?branch=master&service=github)](https://coveralls.io/github/ekino/phpstan-banned-code?branch=master)
[![Total Downloads](https://poser.pugx.org/ekino/phpstan-banned-code/downloads)](https://packagist.org/packages/ekino/phpstan-banned-code)

This library is based on [PHPStan](https://phpstan.org/) to detect calls to specific functions you don't want in your project.
For instance, you can add it in your CI process to make sure there is no debug/non standard code (like [var_dump](https://www.php.net/manual/en/function.var-dump.php), [exit](https://www.php.net/manual/en/function.exit.php), ...).

## Basic usage

To use this extension, require it using [Composer](https://getcomposer.org/):

```bash
composer require --dev ekino/phpstan-banned-code
```

When you use https://github.com/phpstan/extension-installer you are done.

If not, include `extension.neon` in your project's PHPStan config:

```
includes:
	- vendor/ekino/phpstan-banned-code/extension.neon
```

## Advanced usage

You can configure this library with parameters:

```
parameters:
	banned_code:
		nodes:
			# enable detection of echo
			-
				type: Stmt_Echo
				functions: null

			# enable detection of eval
			-
				type: Expr_Eval
				functions: null

			# enable detection of die/exit
			-
				type: Expr_Exit
				functions: null

			# enable detection of a set of functions
			-
				type: Expr_FuncCall
				functions:
					- debug_backtrace
					- dump
					- exec
					- passthru
					- phpinfo
					- print_r
					- proc_open
					- shell_exec
					- system
					- var_dump

		# enable detection of `use Tests\Foo\Bar` in a non-test file
		use_from_tests: true
```

`type` is the returned value of a node, see the method `getType()`.
