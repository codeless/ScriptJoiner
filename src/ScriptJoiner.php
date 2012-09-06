<?php

# Include requirements if not testing:
if (!defined('CODELESS_SCRIPTJOINER_TEST')) {
	require('../vendor/autoload.php');
}

# Start logging:
LogMore::open('scriptjoiner');
LogMore::debug('Including ScriptJoiner');

/**
 * Class: ScriptJoiner
 */
class ScriptJoiner extends PHPParser_PrettyPrinter_Zend {

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
	 * Variable: $comments
	 *
	 * Flag to enable or disable comments in the output. Per default,
	 * comments are enabled.
	 */
	private $comments;


	/**
	 * Variable: $includedFiles
	 *
	 * Table holding the filenames of the files that have been
	 * included while pretty-printing the masterfile as key.
	 * The value is the number of times the file has been included.
	 *
	 * Example of the contents of $includedFiles:
	 * > array(
	 * > 	'file1' => 1,
	 * > 	'file2' => 3
	 * > )
	 */
	private $includedFiles;


	/**
	 * Function: ScriptJoiner
	 *
	 * The constructor
	 */
	public function ScriptJoiner($masterfile=null, $outfile=null) {
		parent::__construct();

		# Initialize:
		if ($masterfile) {
			$this->setMasterfile($masterfile);
		}
		$this->outfile = $outfile;
		$this->comments = true;
		$this->includedFiles = array();
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
			LogMore::error('Invalid file passed: %s',
				$masterfile);
		}
	}


	/**
	 * Function: setOutfile
	 *
	 * Set the outfile for the PrettyPrinter. If left empty,
	 * output will be printed on the screen.
	 */
	public function setOutfile($outfile) {
		$this->outfile = $outfile;
	}


	/**
	 * Function: __set
	 *
	 * Magic method to handle setting of variables.
	 */
	public function __set($name, $value) {
		LogMore::debug('Calling __set');

		# Initialize masterfile:
		if ($name == 'masterfile') {
			$this->setMasterfile($value);
		} else {
			$this->$name = $value;
		}
	}


	/**
	 * Function: pComments
	 *
	 * Handles comments
	 *
	 * Returns:
	 *
	 * 	If comments are enabled, the comments get returned.
	 * 	If comments are disabled, null is returned
	 */
	public function pComments(array $comments) {
		if ($this->comments) {
			$comments = parent::pComments($comments);
		} else {
			$comments = null;
		}

		return $comments;
	}


	/**
	 * Function: pExpr_Include
	 *
	 * Handles the inclusion of script-files.
	 */
	public function pExpr_Include(PHPParser_Node_Expr_Include $node) {
		$file_to_include = $node->expr->value;

		if ($file_to_include) {
			LogMore::debug('File to include: %s', $file_to_include);

			# If the file should be only included/required once
			if ( 	$node->type == PHPParser_Node_Expr_Include::TYPE_INCLUDE_ONCE ||
				$node->type == PHPParser_Node_Expr_Include::TYPE_REQUIRE_ONCE)
			{
				# If the file has already been included
				if (isset($this->includedFiles[$file_to_include])) {
					LogMore::debug('File has already been included once');

					# Leave function
					return null;
				}
			}

			$code = $this->parseFile($file_to_include);

			# Add file to array of included files and raise counter:
			if (isset($this->includedFiles[$file_to_include])) {
				$this->includedFiles[$file_to_include] += 1;
			} else {
				$this->includedFiles[$file_to_include] = 1;
			}

			return $code;
		} else {
			return parent::pExpr_Include($node);
		}
	}


	/**
	 * Function: run
	 *
	 * Parses and prints the masterfile either to stdout or
	 * to the outfile.
	 *
	 * Returns:
	 *
	 * 	true - When all statements got parsed and the outfile 
	 * 		was written
	 * 	false - When an error occured
	 */
	public function run() {
		$rc = false;

		if ($this->masterfile) {
			# Get projectfolder:
			$mainfolder = dirname($this->masterfile);

			# Get working directory:
			$workingdir = getcwd();

			# Switch to projectfolder
			chdir($mainfolder);

			# Process masterfile:
			$program = $this->parseFile(basename($this->masterfile));

			# Add PHP tags:
			$program = '<?php' . PHP_EOL . $program;

			# Switch back to workingdir:
			chdir($workingdir);

			# If program should get written to file
			if ($this->outfile) {
				file_put_contents($this->outfile, $program);
			} else {
				echo $program;
			}

			$rc = true;
		}

		return $rc;
	}


	/**
	 * Function: parseFile
	 *
	 * Returns:
	 *
	 * 	The pretty-printed PHP code
	 */
	private function parseFile($file) {
		LogMore::debug('parsing file %s', $file);
		$statements = file_get_contents($file);

		# Create Parser
		$parser = new PHPParser_Parser(new PHPParser_Lexer);

		# Create syntax tree
		$syntax_tree = $parser->parse($statements);
		LogMore::debug('Syntax tree parsed');

		# Pretty print syntax tree/convert syntax tree back to PHP statements:
		return $this->prettyPrint($syntax_tree);
	}

};


# To be able to use ScriptJoiner directly from the commandline, the
# first argument must be set to the current filename:
if (isset($argv) && isset($argv[0]) && basename($argv[0]) == 'ScriptJoiner.php') {
	# If a mainfile has been passed:
	if (isset($argv[1])) {
		LogMore::debug('Running ScriptJoiner from the commandline');
		$mainfile = $argv[1];

		# Run ScripJoiner:
		$s = new ScriptJoiner($mainfile);
		$s->run();
	}
}
