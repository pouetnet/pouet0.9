# Contribs folder

In order to run a development version of pouet.net on your platform, you need
to have a HTTP webserver, PHP and MySQL.

## How to contribute ?

First thing, you need to [fork](https://github.com/lra/pouet.net/fork) it. Then
you can start making changes on your fork.
When done, you can create a
[pull request](https://help.github.com/articles/using-pull-requests) and a
people will review your change and test it on a staging version of the
website.
When your PR is ready to go live, a contributor will merge it into master and
your proposed changes will automatically go live on [Pouët](http://pouet.net)
in the next 5 minutes.

## When is a new version deployed ?

Anytime a commit is pushed into the `master` branch, Pouët gets a call from
Github and starts deploying a new version.

## Setup a dev environment

#### Quick HOWTO

1. Install an HTTP server, PHP and MySQL
1. Clone the pouet.net repository
1. Point the root directory of your HTTP server to the pouet.net source code,
   with PHP activated
1. In MySQL, create a `pouet` database, which can be accessed by a `pouet` user
   with a `pouet` password from `localhost` (see below)
1. Inject the sample data `contribs/pouet.sql` in your MySQL server (see below)

##### Create the MySQL database and user

```sql
CREATE DATABASE `pouet`;
CREATE USER 'pouet'@'localhost' IDENTIFIED BY 'pouet';
GRANT ALL PRIVILEGES ON pouet.* TO 'pouet'@'localhost';
```

##### Inject the sample data

```bash
mysql -upouet -ppouet pouet < /Users/you/src/pouet.net/contribs/pouet.sql
```

For more details, take a look at the instructions given for each platform below,
they can help.

### OS X

1. Clone the pouet.net repo, let's say it's now in `/Users/you/src/pouet.net`
1. Install [MAMP](http://www.mamp.info/), it's free
1. Launch it
1. In the preferences, on the Apache pane, put `/Users/you/src/pouet.net` as the
document root
1. Start the servers

Now you need to create the `pouet` user and the `pouet` database in your newly
installed MySQL server.

Run `/Applications/MAMP/Library/bin/mysql -uroot -proot` and execute the
following SQL commands:

```sql
CREATE DATABASE `pouet`;
CREATE USER 'pouet'@'localhost' IDENTIFIED BY 'pouet';
GRANT ALL PRIVILEGES ON pouet.* TO 'pouet'@'localhost';
```

Once done, you test your MySQL user by doing:
```bash
/Applications/MAMP/Library/bin/mysql -upouet -ppouet pouet
```

*I suggest you install [Sequel Pro](http://www.sequelpro.com/) for all the MySQL
stuff, it's free too !*

Next, you want some starting data to play with, let's inject the copyleft data
that's been given up to your by poueters:
```bash
/Applications/MAMP/Library/bin/mysql -upouet -ppouet pouet < /Users/you/src/pouet.net/contribs/pouet.sql
```

Now you should be able to open your local pouet on
[http://localhost:8888/](http://localhost:8888/) !

Well done sir !
