# Description

PHP ScriptJoiner is a tiny PHP-Script used to generate one-file PHP applications out of multi-file PHP applications. One-file PHP applications are easier to deploy than multi-file PHP applications, while maintaining applications is more comfortable with multiple files.

Because of that, PHP ScriptJoiner is merely a deployment tool for PHP applications.


# Usage

Assumption: the two files file1.php and file2.php form a PHP application and should get distributed in one file only (with the name app.php).

 	<?php
 	# file1.php:
 	echo 'File 1',PHP_EOL;
 	require('file2.php');

 	<?php
 	# file2.php:
 	echo 'File 2',PHP_EOL;

To combine these two files into app.php, a short PHP scripts needs to be written:

 	require('vendor/codeless/scriptjoiner/src/ScriptJoiner.php');

 	$s = new ScriptJoiner();
 	$s->setMasterfile('file1.php');
 	$s->setOutfile('app.php');
 	$s->run();

A shorter version would be:

 	$s = new ScriptJoiner('file1.php', 'app.php');

The result looks like this:

 	<?php
 	# file1.php:
 	echo 'File 1',PHP_EOL;
 	# file2.php:
 	echo 'File 2',PHP_EOL;

Running PHP ScriptJoiner directly from the commandline:

 	php -f /path/to/ScriptJoiner.php /path/to/project/mainfile.php 1> /path/to/outfile.php

Note the "1>", which redirects only the stdout to the outfile; PHP ScriptJoiner will use the stderr-channel for logging.


# Other methods to join PHP scripts

Using PHC, the open source PHP Compiler at http://www.phpcompiler.org/, it should be possible to combine many php scripts into a single file.


# Credits and Bugreports

PHP ScriptJoiner was written by Codeless (http://www.codeless.at/). All bugreports can be directed to more@codeless.at. Even better, bugreports are posted on the github-repository of this package: https://www.github.com/codeless/scriptjoiner
