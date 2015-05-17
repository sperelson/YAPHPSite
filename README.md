# YAPHPSite
Yet another PHP Website. Built as a programming recruitment test. No framework was used. Except for the one built from scratch for this site of course. Requires PHP 5.5 or better.

## Get Started
First set up the MySQL database by running the db.sql script.

This will create the DB and all the tables and triggers for the site.

This site makes use of features only available in PHP 5.5 or better.

## Running
The simplest approach to running the site, if the DB is running locally, is to use the PHP local Web server:

```php -S localhost:8080 server.php ```

Once the site is running, connect to:

```http://localhost:9999/operator/init ```

This will initialise a user in the operators table in the DB and will display the username:password.

Log in using the provided credentials.

## Interest Points
The site's framework is a fairly standard MVC structure with controllers, models, and views. The framework is vaguely reminiscent of an early CodeIgniter.

The lifecycle of a page request:
* Load all the classes (boot/boot.php)
* Start the router (router.php)
* Find a corresponding controller/action and execute
* redirect or generate a view

The framework has helpers for storing some specialised session variables such as errors and flash data. This makes dealing with forms a lot more usable. You get error notifications and data is not lost between reloads.

URLs take on the following pattern:

```http://domain/{controller}/{action}[/{var1}[/{var2}[/{var3}]]] ```

The variables are placed into a segments array that is accessible via ```Apps::segment()``` function.

The end result is yet another PHP micro-framework.

