## Library Management System

A simple library management system.

## Server Requirements

* PHP >= 5.6.4
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension

## Installation

* execute `composer install` or `composer install --no-plugins --no-scripts`
* configure .env file at the root of the project
* execute `php artisan key:generate`
* execute `php artisan migrate`
* execute `php artisan db:seed`

## Sample Accounts

The seeder has at least two usable user accounts as a admin and member. For member account use ariel@example.com will password as password and admin@example.com with password as password.

## Others

The project has task scheduler you might want to the cron job below.

*  ` * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`


