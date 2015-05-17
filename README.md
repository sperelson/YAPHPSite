# YAPHPSite
Yet another PHP Website. Built as a programming recruitment test. No framework was used. Except for the one built from scratch for this site of course. Requires PHP 5.5 or better.

## Get Started
First set up the MySQL database by running the db.sql script. Make sure the /boot/config.php file is updated with the correct database details and user credentials.

Importing the db.sql script will create the database and all of the tables and triggers for the site.

This site makes use of features only available in PHP 5.5 or better.

## Running
The simplest approach to running the site, if the DB is running locally, is to use the PHP local Web server:

```php -S localhost:8080 server.php ```

Once the site is running, connect to:

```http://localhost:8080/operator/init ```

This will initialise a user in the operators table in the DB and will display the username:password.

Log in using the provided credentials.

## Interest Points
The site's coded from scratch framework is a fairly standard MVC structure with controllers, models, and views.

The lifecycle of a page request:
* Load all the classes (boot/boot.php)
* Start the router (router.php)
* Find a corresponding controller/action and execute (/controllers/*)
* redirect or generate a view

The framework has various helpers. One of which is used for creating views and another for storing some specialised session variables such as errors and flash data.

Flash data makes dealing with forms a lot more usable. You're able to surface error notifications and data is not lost between reloads.

URLs take on the following pattern:

```http://domain/{controller}/{action}[/{var1}[/{var2}[/{var3}]]] ```

The variables are placed into a segments array that is accessible via ```Apps::segment()``` function.

The end result is yet another PHP micro-framework.

