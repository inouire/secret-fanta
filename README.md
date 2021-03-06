secret-fanta
============

PHP secret santa app with Symfony2 components

### Install it ###

Requirements:

- PHP 5.3+
- git
- composer (see http://getcomposer.org)
- a working mail server

Clone project repository with git:
```bash
git clone https://github.com/inouire/secret-fanta.git
```

Install dependencies with composer:
```bash
cd secret-fanta
composer install
```

### Use it ###

#### Configure ####

Complete config file `conf/participants.yml` with the list of participants and the couples if any

```yaml
# information about the participants

#list of participants with format "name": email
people:
    "Zorglub": zorglub@dupuis.fr.fake
    "Sylvain": sylvain@chaumiere.fr.fake
    "Sylvette": sylvette@chaumiere.fr.fake
    "Mickey": mickey@disney.com.fake
    "Minnie": minnie@disney.com.fake

# list of couples with format - ["name1", "name2"]
couples:
    - ["Mickey", "Minnie"]
    - ["Sylvain", "Sylvette"]
```

Modify email template file `conf/mail_content.html` to match your needs.
Just be sure to have `{{name}}` and `{{target}}` keywords so that the email will contain the useful information.

#### Run ####

Try your configuration in debug mode and with a bypass email (all emails will be sent to the bypass address)
```bash
php santa.php reindeer:unleash --bypass=your@email --debug
```
The purpose of debug mode is to display who offers a gift to who, and should not be used for the real sending as the secret would be lost.

Check your inbox, and if everything looks good you can launch the program for real:
```bash
php santa.php reindeer:unleash
```
