<?php

require('src/ScriptJoiner.php');

class ScriptJoinerTest extends PHPUnit_Framework_TestCase {

	/**
	 * Compiles all projects into one-file scripts
	 * and checks if the output is as wanted.
	 */
	public function testProjects() {
		$numberOfProjects = 4;
		$p = new ScriptJoiner();

		for ($i=1; $i<=$numberOfProjects; $i++) {
			# Compile filenames:
			$testdir = 'tests/project';
			$masterfile = $testdir . $i . '/file1.php';
			$outfile = $testdir . $i . '_outfile.php';
			$valid_outfile = $testdir . $i . '_valid.php';

			# Initialize ScriptJoiner:
			$p->setMasterfile($masterfile);
			$p->setOutfile($outfile);
			$p->run();

			# Get md5-hashes of outfiles
			$md5_outnow = md5_file($outfile);
			$md5_outvalid = md5_file($valid_outfile);

			# Comparison of files via md5-hash
			$this->assertEquals($md5_outnow, $md5_outvalid);
		}
	}

};
