secret-fanta
============

PHP secret santa app with Symfony2 components

### Install it ###

Requirements:

- PHP 5.3+
- git
- composer (see http://getcomposer.org)

Clone project repository with git:
```bash
git clone git@github.com:inouire/secret-fanta.git
```

Install dependencies with composer:
```bash
cd secret-fanta
composer install
```

### Use it ###


Complete config file `conf/participants.yml` with the list of participants 
for your secret santa with format `"name": email`

Launch program:
```bash
php santa.php reindeer:unleash
```
