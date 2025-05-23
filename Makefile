.PHONY: app-composer-validate app-cs-check app-cs-fix app-install app-security-check app-static-analysis app-test \
app-test-with-code-coverage ci

default: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?##.*$$' $(MAKEFILE_LIST) | sort | awk '{split($$0, a, ":"); printf "\033[36m%-30s\033[0m %-30s %s\n", a[1], a[2], a[3]}'

app-composer-validate: ## to validate composer config
	composer validate
	composer normalize --dry-run

app-cs-check: ## to show files that need to be fixed
	PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix --dry-run --diff --verbose

app-cs-fix: ## to fix files that need to be fixed
	vendor/bin/php-cs-fixer fix --verbose

app-install: ## to install app
	composer install --prefer-dist

app-security-check: ## to check if any security issues in the PHP dependencies
	composer audit

app-static-analysis: ## to run static analysis
	vendor/bin/phpstan analyze --memory-limit=-1

app-test: ## to run unit tests
	vendor/bin/phpunit --no-coverage

app-test-functional: ## test some code snippets are detected as banned code
	@for filename in $$(find tests/Functional/snippets -type f -name *.php); do \
		if vendor/bin/phpstan analyze $$filename -l 7 | grep -q 'Should not use'; then \
			echo "Code snippet $$filename was correctly detected as banned code."; \
		else \
			echo "Code snippet $$filename was NOT detected as banned code, but it SHOULD be."; \
			exit 1; \
		fi \
	done \

app-test-with-code-coverage: ## to run unit tests with code-coverage
	@php -m | grep -qE 'xdebug|pcov' || (echo "Please install Xdebug or PCOV to enable code coverage." && exit 1)
	vendor/bin/phpunit --coverage-text --colors=never

ci: ## to run checks during ci
	make app-composer-validate app-test-with-code-coverage app-test-functional app-static-analysis app-cs-check app-security-check
