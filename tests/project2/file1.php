<?php

if (1) {
	require('file2.php');
} else {
	echo 'Before including file3';
	require('file3.php');
}
