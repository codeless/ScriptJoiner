#! /bin/bash

# Generates the "valid" outfiles, which need to get controlled manually

for project in 1 2 3 4 5
do
	infile="project"$project"/file1.php"
	outfile="project"$project"_valid.php"
	`php -f ../src/ScriptJoiner.php $infile 1> $outfile`
done
