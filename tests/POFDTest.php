<?php

require('src/POFD.php');

class POFDTest extends PHPUnit_Framework_TestCase {

	/**
	 * Compiles all projects into one-file scripts
	 * and checks if the output is as wanted.
	 */
	public function testProjects() {
		$numberOfProjects = 4;
		$p = new POFD();

		for ($i=1; $i<=$numberOfProjects; $i++) {
			# Compile filenames:
			$masterfile = 'tests/project' . $i . '/file1.php';
			$outfile = 'tests/project' . $i . '_outfile.php';
			$valid_outfile = 'tests/project' . $i . '_valid.php';

			# Initialize POFD:
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
