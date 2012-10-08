**ScriptJoiner is replaced by JuggleCode (<https://github.com/codeless/jugglecode>) and will not get maintained anymore, as of 2012-10-08**


Title: Description

PHP ScriptJoiner is a tiny PHP-Script used to generate one-file PHP applications out of multi-file PHP applications. One-file PHP applications are easier to deploy than multi-file PHP applications, while maintaining applications is more comfortable with multiple files.

Because of that, PHP ScriptJoiner is merely a deployment tool for PHP applications.


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

: 	require('vendor/codeless/scriptjoiner/src/ScriptJoiner.php');
:
: 	$s = new ScriptJoiner();
: 	$s->masterfile = 'file1.php';
: 	$s->outfile = 'app.php';
: 	$s->run();

A shorter version would be:

: 	$s = new ScriptJoiner('file1.php', 'app.php');
: 	$s->run();

The result looks like this:

: 	<?php
: 	# file1.php:
: 	echo 'File 1',PHP_EOL;
: 	# file2.php:
: 	echo 'File 2',PHP_EOL;

To disable comments in the output, use:

: 	$s->comments = false;

Running PHP ScriptJoiner directly from the commandline:

: 	php -f /path/to/ScriptJoiner.php /path/to/project/mainfile.php 1> /path/to/outfile.php

Note the "1>", which redirects only the stdout to the outfile; PHP ScriptJoiner will use the stderr-channel for logging.


Title: Other methods to join PHP scripts

Using PHC, the open source PHP Compiler at <http://www.phpcompiler.org/>, it should be possible to combine many php scripts into a single file.


Title: Detailed description

ScriptJoiner is just a configurable PrettyPrinter for PHP.


Title: Credits and Bugreports

PHP ScriptJoiner was written by Codeless (<http://www.codeless.at/>). All bugreports can be directed to more@codeless.at. Even better, bugreports are posted on the github-repository of this package: <https://www.github.com/codeless/scriptjoiner>


Title: License

This work is licensed under a Creative Commons Attribution 3.0 Unported License: <http://creativecommons.org/licenses/by/3.0/deed.en_US>
