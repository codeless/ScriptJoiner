Title: Description

PHP-Application One File Distributor (short: POFD) is a tiny PHP-Script used to generate one-file PHP applications out of multi-file PHP applications. One-file PHP applications are easier to deploy than multi-file PHP applications,  while maintaining is probably more comfortable with multiple files.

Because of that, POFD is merely a deployment tool for PHP applications.


Title: Usage

Assumption: the two files file1.php and file2.php form a PHP application and should get distributed in one file only (with the name app.php).

: 	<?php
: 	# file1.php:
: 	echo 'File 1',PHP_EOL;
: 	require('file2.php');

: 	<?php
: 	# file2.php:
: 	echo 'File 2',PHP_EOL;

To combine these two files into app.php, a short PHP scripts needs to be written:

: 	require('vendor/codeless/pofd/src/pofd.php');
:
: 	$p = new POFD();
: 	$p->setMasterfile('file1.php');
: 	$p->setOutfile('app.php');
: 	$p->run();

A shorter version would be:

: 	$p = new POFD('file1.php', 'app.php');


Title: Credits and Bugreports

POFD was written by Codeless (<http://www.codeless.at/>). All bugreports can be directed to more@codeless.at. Even better, bugreports are posted on the github-repository of this package: <https://www.github.com/codeless/pofd>
