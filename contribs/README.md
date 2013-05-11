# Contribs folder

In order to run a development version of pouet.net on your platform, you need
to have a HTTP webserver, PHP and MySQL.

## OS X

1. Clone the pouet.net repo, let's say it's now in `/Users/you/src/pouet.net`
1. Install (MAMP)[http://www.mamp.info/], it's free
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

*I suggest you install (Sequel Pro)[http://www.sequelpro.com/] for all the MySQL
stuff, it's free too !*

Next, you want some starting data to play with, let's inject the structure of
the live database:
```bash
/Applications/MAMP/Library/bin/mysql -upouet -ppouet pouet < /Users/you/src/pouet.net/pouet.sql
```

Now you should be able to open your local pouet on http://localhost:8888/ !

To Be Continued...
