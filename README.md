# PHPStan Banned Code

## Basic usage

To use this extension, require it using [Composer](https://getcomposer.org/):

```bash
composer require --dev ekino/phpstan-banned-code
```

And include extension.neon in your project's PHPStan config:

```neon
includes:
	- vendor/ekino/phpstan-banned-code/extension.neon
```

## Advanced usage

You can configure this library with parameters:

```neon
parameters:
	banned_code:
		nodes:
			# enable detection of echo
			-
				type: Stmt_Echo

			# enable detection of eval
			-
				type: Expr_Eval

			# enable detection of die/exit
			-
				type: Expr_Exit

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
