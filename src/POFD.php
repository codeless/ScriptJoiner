<?php

# If LogMore has not been included yet
if (!class_exists('LogMore')) {
	# Try to find it
	$paths = array(
		'LogMore.php',
		'../vendor/codeless/logmore/src/LogMore.php',
		'vendor/codeless/logmore/src/LogMore.php'
	);
	foreach ($paths as $p) {
		if (is_file($p)) {
			require_once($p);
			break;
		}
	}
}

# Include the PHP-Parser and the FileLexer:
if (is_dir('vendor')) {
	require('vendor/autoload.php');
} else {
	require('../vendor/autoload.php');
}
require('FileLexer.php');


/**
 * Class: POFD
 */
class POFD {

	/**
	 * Variable: $masterfile
	 *
	 * Absolute or relative path to the projects master-/mainfile.
	 */
	private $masterfile;


	/**
	 * Variable: $outfile
	 *
	 * Absolute or relative path to the projects outfile. If the 
	 * outfile is empty or null, the final script will get printed
	 * on stdout.
	 */
	private $outfile;


	/**
	 * Function: POFD()
	 *
	 * The constructor
	 */
	public function POFD($masterfile=null, $outfile=null) {
		# Start logging
		LogMore::open('pofd');

		# Initialize:
		$this->setMasterfile($masterfile);
		$this->setOutfile($outfile);
	}


	/**
	 * Function: setMasterfile
	 *
	 * Validates the passed filepath to the applications masterfile
	 * for existance and readability.
	 */
	public function setMasterfile($masterfile) {
		if (is_file($masterfile) && is_readable($masterfile)) {
			LogMore::info('Valid file passed');
			$this->masterfile = $masterfile;
		} else {
			LogMore::info('Invalid file passed: %s',
				$masterfile);
		}
	}


	/**
	 * Function: setOutfile
	 */
	public function setOutfile($outfile) {
		$this->outfile = $outfile;
	}


	/**
	 * Function: run
	 *
	 * Parses all statements of the masterfile and injects the
	 * statements of files to include.
	 *
	 * Returns:
	 *
	 * 	true - When all statements got parsed and the outfile 
	 * 		was written
	 * 	false - When an error occured
	 */
	public function run() {
		$rc = false;

		# If both master- and outfile are given
		if ($this->masterfile) {
			# Get projectfolder:
			$mainfolder = dirname($this->masterfile);

			# Get working directory:
			$workingdir = getcwd();

			# Switch to projectfolder
			chdir($mainfolder);

			# Process masterfile:
			$statements = $this->processFile(
				basename($this->masterfile));

			# Switch back to workingdir:
			chdir($workingdir);

			# Convert statements into valid PHP code:
			$prettyPrinter = new PHPParser_PrettyPrinter_Zend;
			$code = '<?php ' . PHP_EOL .
				$prettyPrinter->prettyPrint($statements);

			# Export statements into outfile
			if ($this->outfile) {
				file_put_contents($this->outfile, $code);
			} else {
				echo $code;
			}

			# Set rc accordingly...
		}

		return $rc;
	}


	/**
	 * Function: processFile
	 *
	 * Parameters:
	 *
	 * 	$file - The file to parse, relative to the directory
	 * 		of the masterfile
	 *
	 * Returns:
	 *
	 * 	An array of statements.
	 */
	private function processFile($file) {
		# Create Parser
		$parser = new PHPParser_Parser(new FileLexer);

		# Parse file into statements
		$statements = $parser->parse($file);

		# Parse statements
		return (sizeof($statements))
			? $this->parseStatements($statements)
			: null;
	}


	/**
	 * Function: parseStatements
	 *
	 * Wrapper-Function for parseStatement.
	 */
	private function parseStatements($statements) {
		return $this->parseStatement($statements);
	}


	/**
	 * Function: parseStatement
	 */
	private function parseStatement($stmts, $i=0) {

		# Get statement to inspect:
		$s = $stmts[$i];
		LogMore::debug('*** Next statement');

		# Get subnodes
		$subnodes = (method_exists($s, 'getSubNodeNames'))
			? $s->getSubNodeNames()
			: null;

		# If statement has subnodes
		if ($subnodes) {
			LogMore::debug('Number of subnodes: ' . sizeof($subnodes));

			# Loop through subnodes
			$s = $this->parseSubnodes($s, $subnodes);
		}

		# If statement has a type:
		if (method_exists($s, 'getType')) {
			# Include statement:
			if ($s->getType() == 'Expr_Include') {
				$stmts = $this->parseIncludeStatement($s, $stmts, $i);
			} else {
				LogMore::debug('Type of statement: %s', $s->getType());
			}
		}


		# Raise index
		$i += 1;

		# If End of statements reached, return whole statements.
		# Else, recall parseStatement with new index:
		return (isset($stmts[$i]))
			? $this->parseStatement($stmts, $i)
			: $stmts;
	}

	private function parseIncludeStatement($s, $stmts, $i) {
		# Inject include-stmts
		$file = $s->expr->__get('value');

		if ($file) {
			# Typ des Includes abfragen;
			# Included files in einem array speichern

			syslog(LOG_DEBUG, 'Include: ' . $file);
			$add_stmts = $this->processFile($file);

			# Remove include-statement from statements and
			# inject with the new ones:
			array_splice($stmts, $i, 1, $add_stmts);

			# Reset:
			$i = -1;
		} else {
			LogMore::debug('Inclusion of dynamic files is not supported');
		}

		return $stmts;
	}

	private function parseSubnodes($s, $subnodes) {
		foreach ($subnodes as $sn) {
			$sub_stmts = $s->__get($sn);

			# If subnode has statements
			if (sizeof($sub_stmts) && is_array($sub_stmts)) {
				LogMore::debug('Subnode: %s, statements: %d',
					$sn,
					sizeof($sub_stmts));

				# Process statements
				$sub_stmts = $this->parseStatements($sub_stmts);
				$s->__set($sn, $sub_stmts);
				#$stmts[$i]->__set($sn, $sub_stmts);
			} elseif (is_object($sub_stmts)) {
				LogMore::debug('Class of sub-statement is: %s',
					get_class($sub_stmts));
				LogMore::debug('Type of sub-statements is %s',
					$sub_stmts->getType());
				$subsub_stmts = $sub_stmts->__get('stmts');
				$subsub_stmts = $this->parseStatements($subsub_stmts);
				$sub_stmts->__set('stmts', $subsub_stmts);
			}
		}

		return $s;
	}

};
