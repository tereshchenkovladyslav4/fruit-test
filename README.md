Fruits Test Task
========================

* Write a console command for getting all fruits from and saving them into DB.

* Everytime new fruit(s) is added, send an email about it to a dummy admin email (e.g. test@gmail.com or your gmail address).

* Create a page with all fruits (paginated). Add a form to filter fruits by name and family. Each fruit can be added to favorites (up to 10
  fruits).

* Create a page with favorite fruits. Display the sum of nutritions facts of all fruits.

* Add a README file with installation and startup instructions.

* Treat the task as a full-fledged project. In php follow PSR-12, in JS follow JavaScript Standard Style. Unit tests are welcome. You should
  use the Symfony PHP framework.

* Once the project code is ready please upload it to a GitHub repo and share with us the GitHub repo URL.

Requirements
------------

* PHP 8.1.0 or higher;
* PDO-SQLite PHP extension enabled;
* and the [usual Symfony application requirements][2].

Installation
------------

Download project code and install packages:

```bash
$ git clone https://github.com/sha02viacheslav/fruit-test.git fruit-test
$ cd fruit-test/
$ composer install
```

Make local env file and set mailgun server:

```bash
$ cd fruit-test/
$ touch .env.local
$ composer install

MAILER_DSN=mailgun+smtp://USERNAME:PASSWORD@default?region=us
```

-----

There's no need to configure anything before running the application. There are
2 different ways of running this application depending on your needs:

**Option 1.** [Download Symfony CLI][5] and run this command:

```bash
$ cd fruit-test/
$ symfony serve
```

Then access the application in your browser at the given URL (<https://localhost:8001> by default).

**Option 2.** Use a web server like Nginx or Apache to run the application
(read the documentation about [configuring a web server for Symfony][3]).

On your local machine, you can run this command to use the built-in PHP web server:

```bash
$ cd fruit-test/
$ php -S localhost:8000 -t public/
```

Fruite Migration
-----

```bash
$ cd fruit-test/
$ php bin/console app:migrate-fruits --send-to=your_email@email.com
```

Tests
-----
Execute this command to run tests:

```bash
$ cd fruit-test/
$ ./bin/phpunit
```

