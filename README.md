# phyex
Application for evaluation physio exercise by physiotherapist

## Installation

1. Pull current version from git `git pull`
2. Create `.env.local` file and set **DATABASE_URL** variable for local connection to database
3. Install PHP dependencies `composer install`
4. Install Javascript dependencies `yarn install`
5. Create database `symfony console doctrine:database:create`
6. Create database schema `symfony console doctrine:schema:create`

## Tools
Get password Hash for user `php bin/console security:hash-password`
Install fixtures `php bin/console doctrine:fixtures:load`


## Maker
- Create new entity `symfony console make:entity`
- Create new controller `symfony console make:controller`
- Create new fixture `symfony console make:fixture`

## Bugs
For fix known issue in Faker change
`return join($words, ' ') . '.';` to `return join(' ', $words) . '.';` in file
`vendor/fzaninotto/faker/src/Faker/Provider/Lorem.php`
according to https://stackoverflow.com/questions/67592213/join-argument-2-array-must-be-of-type-array-string-given
