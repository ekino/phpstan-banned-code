CHANGELOG
=========

master
------

* todo...

v3.1.0
------

* Added empty() to the list of banned functions
* Updated symfony/var-dumper to 6.4
* Updated phpunit/phpunit to 10.5
* Updated nikic/php-parser to 5.4
* Move code snippets into tests directory
* Replace `fabpot/local-php-security-checker` by `composer audit` for the security check

v3.0.0
------

* Updated to PHPStan 2.0

v2.1.0
------

* Added option to prevent errors to be ignored
* Added error identifiers starting with `ekinoBannedCode.`

v2.0.0
------

* Drop support for PHP ^7.3 || ^8.0, support now only ^8.1
* Force usage of native sprintf function
* Added rule to ban shell execution via backticks
* Added rule to ban print statements
* Allow Composer plugin ergebnis/composer-normalize
* Add Composer keyword for asking user to add this package to require-dev instead of require
* Normalize leading backslashes in banned function names

v1.0.0
------

* Improve PHPStan configuration file
* Updated to PHPStan 1.0
* Updated to PHPUnit 9.5
* Drop support for PHP 7.2

v0.5.0
------

* Migrate from Travis to GitHub Actions
* Fix deprecated PHP-CS rules 

v0.4.0
------

* Drop support for PHP 7.1
* Added support of dd function
* Replaced deprecated localheinz/composer-normalize in favor of ergebnis one
* Allow PHP ^8.0
* Switch to the new security checker
* Upgrade friendsofphp/php-cs-fixer

v0.3.1
------

* Fix deprecated config inside extension.neon file
* Fix tests by adding nikic/php-parser as a required dev dependency

v0.3.0
------

* Updated to PHPStan 0.12
* Added testing on PHP 7.4

v0.2.0
------

* Enable strict typing
* Add coveralls and badges
* Autoinstall through https://github.com/phpstan/extension-installer
* Apply the BannedNodesRule only to named functions
* Add functional tests with some code snippets that should be detected when running phpstan
