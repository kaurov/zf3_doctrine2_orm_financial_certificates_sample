Financial Certificates Sample
==================================================

This sample is based on the Hello World sample and it shows how to:

  * Integrate your web site with Doctrine library
  * Initialize database schema
  * Use entity manager
  * Use domain driven programming using Zend Framework 3
  * Create entities and define relations between entities
  * Create repositories
  * Use unit-tests
  * XML output
  * Factory Pattern

## Installation

You need to have Apache 2.4 HTTP server, PHP v.5.6 or later and MySQL v.5.6 or later.

Download the sample to some directory (it can be your home dir or `/var/www/html`) and run Composer as follows:

```
php composer.phar install
```

The command above will install the dependencies (Zend Framework and Doctrine).

Enable development mode:

```
php composer.phar development-enable
```

Adjust permissions for `data` directory:

```
sudo chown -R www-data:www-data data
sudo chmod -R 775 data
```

Create `config/autoload/local.php` config file by copying its distrib version:

```
cp config/autoload/local.php.dist config/autoload/local.php
```

Edit `config/autoload/local.php` and set database password parameter.

Login to MySQL client:

```
mysql -u root -p
```

Create database:

```
CREATE DATABASE zf3certificate;
GRANT ALL PRIVILEGES ON zf3certificate.* TO zf3certificate@localhost identified by '<your_password>';
quit
```

Create tables and import data to database:

```
mysql -u root -p zf3certificate < data/schema.mysql.sql
```

Alternatively, you can run database migrations:

```
./vendor/bin/doctrine-module migrations:migrate
```

Then create an Apache virtual host. It should look like below:

```
<VirtualHost *:80>
    DocumentRoot /path/to/zf3certificate/public
    
	<Directory /path/to/zf3certificate/public/>
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
```
After creating the virtual host, restart Apache.

Now you should be able to see the Fin.Certificates website by visiting the link "http://localhost/". 
 
## License

This code is provided under the [BSD-like license](https://en.wikipedia.org/wiki/BSD_licenses). 

## The task solved. Case Study: Financial Website
### Introduction
Our core business is to create financial websites for some of the top-tier financial institutions in the world. Those pages display information about financial products or so-called "certificates". We ask you to build a simple webpage which can display the characteristics of the certificates. Your code should be written in PHPS and be fully object-orientated. Using Zend Framework is not required, but would be a real plus.

### Tasks

1. The certificates are the most important element in your small application. 
A certificate has the following properties:
ISIN
Trading Market (e.g. Frankfurt, London)
Currency
Issuer
Issuing Price
Current Price
Please create a class "Certificate" with the above proper-ties, which should also be changeable on demand. You can design the class in the way you prefer and also add other classes when it's necessary.

2. Each certificate has a history of prices which update every few seconds. Additionally, each product can have documents assigned to it. The documents may have varying types, e.g. a "Term Sheet"as a PDF document.

Please model those associations into your code.

3. Create two functions "displayAsHtml()" and "displa-yAsXml(); which render a certificate accordingly. This should happen as a HTML page and a XML file.

4. In addition to the standard certificates, there are special types of certificates.

Those feature additional properties:

Bonus-Certificates have a barrier level and you need to know if this barrier was hit or not.
Guarantee-Certificates have a participation rate.

5. Please create a class "BonusCertificate" and "Guarantee-Certificate" and ensure that those special properties are also used in the display-Functions of Number 3.

Additionally, please take care that Guarantee-Certificates mustn't be exported as XML. In such a case, please fire a meaningful exception.

6. Please send us your code including all used frameworks and libraries in a single zip-File. If you want to get some extra points, we would love if you could send a simple class diagram with it. If you have any experience with Unit Tests in PHP, we would be also very keen to see it in this example; otherwise we'll show you how to do when you join us!
