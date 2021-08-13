CHANGELOG
=========

master
------

* Fix deprecated PHP-CS rules 

v0.4.0
------

* Drop support for PHP 7.1
* Added support of dd function
* Replaced deprecated localheinz/composer-normalize in favor of ergebnis one
* Allow PHP ^8.0
* Switch to the new security checker

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
