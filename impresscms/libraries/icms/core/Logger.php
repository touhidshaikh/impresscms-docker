<?php
/**
 * icms_core_Logger component main class file
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since		XOOPS 2.0
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @category	ICMS
 * @package		Core
 * @subpackage	Logger
 * @version	$Id: Logger.php 12313 2013-09-15 21:14:35Z skenow $
 */

/**
 * Collects information for a page request
 *
 * Records information about database queries, blocks, and execution time
 * and can display it as HTML. It also catches php runtime errors.
 *
 * @since		XOOPS
 * @author		Kazumi Ono  <onokazu@xoops.org>
 * @author		Skalpa Keo <skalpa@xoops.org>
 *
 * @category	ICMS
 * @package		Core
 * @subpackage	Logger
 */
class icms_core_Logger {

	public $queries = array();
	public $blocks = array();
	public $extra = array();
	public $logstart = array();
	public $logend = array();
	public $errors = array();
	public $deprecated = array();
    public $filters = array();

	public $usePopup = false;
	public $activated = true;

	private $renderingEnabled = false;

	/**
	 * Constructor
	 */
	private function __construct() { /* Empty! */ }

	/**
	 * Get a reference to the only instance of this class
	 *
	 * @return  object icms_core_Logger  (@link icms_core_Logger) reference to the only instance
	 * @static
	 */
	static public function &instance() {
		static $instance;
		if (!isset( $instance )) {
			$instance = new icms_core_Logger();
			// Always catch errors, for security reasons
			set_error_handler( array( $instance, "handleError" ) );
			set_exception_handler(array($instance, 'handleException'));
		}
		return $instance;
	}

	/**
	 * This was added in 1.3 because there was a separate class - XoopsErrorHandler -
	 * having the additional class is unnecessary and the methods existed in other forms.
	 *
	 * @param bool $showErrors
	 * @deprecated	Use $activated = TRUE/FALSE, instead
	 * @todo		Remove in version 1.4 - no occurrences in the core
	 */
	public function activate($showErrors = false) {
		icms_core_Debug::setDeprecated('$this->activated = ', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
		$this->activated = $showErrors;
	}

	/**
	 * Enable logger output rendering
	 * When output rendering is enabled, the logger will insert its output within the page content.
	 * If the string <!--{xo-logger-output}--> is found in the page content, the logger output will
	 * replace it, otherwise it will be inserted after all the page output.
	 */
	public function enableRendering() {
		if (!$this->renderingEnabled) {
			ob_start(array(&$this, 'render'));
			$this->renderingEnabled = true;
		}
	}

	/**
	 * Disable logger output rendering.
	 */
	public function disableRendering() {
		if ($this->renderingEnabled) {
			$this->renderingEnabled = false;
		}
	}

	/**
	 * Disabling logger for some special occasion like AJAX requests and XML
	 *
	 * When the logger absolutely needs to be disabled whatever it is enabled or not in the preferences
	 * and whether user has permission or not to view it
	 */
	public function disableLogger() {
		$this->activated = false;
	}

	/**
	 * Returns the current microtime in seconds.
	 * @return float
	 */
	private function microtime() {
		$now = explode(' ', microtime());
		return (float) $now[0] + (float) $now[1];
	}

	/**
	 * Start a timer
	 * @param   string  $name   name of the timer
	 */
	public function startTime($name = 'ICMS') {
		$this->logstart[$name] = $this->microtime();
	}

	/**
	 * Stop a timer
	 * @param   string  $name   name of the timer
	 */
	public function stopTime($name = 'ICMS') {
		$this->logend[$name] = $this->microtime();
	}

	/**
	 * Log a database query
	 * @param   string  $sql    SQL string
	 * @param   string  $error  error message (if any)
	 * @param   int     $errno  error number (if any)
	 */
	public function addQuery($sql, $error=null, $errno=null) {
		if ($this->activated )		$this->queries[] = array('sql' => $sql, 'error' => $error, 'errno' => $errno);
		if (defined('ICMS_LOGGING_HOOK') and ICMS_LOGGING_HOOK != '') {
			include ICMS_LOGGING_HOOK;
		}
	}

	/**
	 * Log display of a block
	 * @param   string  $name       name of the block
	 * @param   bool    $cached     was the block cached?
	 * @param   int     $cachetime  cachetime of the block
	 */
	public function addBlock($name, $cached = false, $cachetime = 0) {
		if ($this->activated )
		$this->blocks[] = array('name' => $name, 'cached' => $cached, 'cachetime' => $cachetime);
	}

	/**
	 * Log extra information
	 * @param   string  $name       name for the entry
	 * @param   int     $msg  text message for the entry
	 */
	public function addExtra($name, $msg) {
		if ($this->activated) {
			$this->extra[] = array('name' => $name, 'msg' => $msg);
		}
	}

	public function addDeprecated($msg) {
		if ($this->activated) {
			$this->deprecated[] = $msg;
		}
	}

	/**
	 * Log display of filters
	 * @param   string  $name       name/id of the content
	 * @param   string  $filter_message     message displayed
	 */
	public function addFilter($name, $filter_message) {
		if ($this->activated )
		$this->filters[] = array('name' => $name, 'filtermsg' => (int) $filter_message);
	}

	/**
	 * Handle exception
	 *
	 * @param Exception $exception
	 */
	public function handleException($exception) {
		icms_loadLanguageFile('core', 'core');

		$errstr = $exception->getMessage();
		$trace = true;
		if (substr($errstr, 0, '8') == 'notrace:') {
			$trace = false;
			$errstr = substr($errstr, 8);
		}
		echo sprintf(_CORE_PAGENOTDISPLAYED, $errstr);
		if ($trace) {
			echo '<br /><div>File: ' . $exception->getFile() . '</div>';
			echo '<div>Line: ' . $exception->getLine() . '</div>';
			echo "<div>Backtrace:<br />";
			$trace = $exception->getTrace();
			array_shift( $trace );
			foreach ( $trace as $step) {
				if (isset($step['file'])) {
					echo $this->sanitizePath($step['file']);
					echo ' (' . $step['line'] . ")\n<br />";
				}
			}
			echo '</div>';
		}
		exit();
	}

    /**
	 * Error handling callback (called by the zend engine)
	 * @param  string  $errno
	 * @param  string  $errstr
	 * @param  string  $errfile
	 * @param  string  $errline
	 */
	public function handleError($errno, $errstr, $errfile, $errline) {
		$errstr = $this->sanitizePath($errstr);
		$errfile = $this->sanitizePath($errfile);
		if ($this->activated && ( $errno & error_reporting() )) {
			// NOTE: we only store relative pathnames
			$this->errors[] = compact('errno', 'errstr', 'errfile', 'errline');
		}

		if ($errno == E_USER_ERROR) {
			$trace = true;
			if (substr($errstr, 0, '8') == 'notrace:') {
				$trace = false;
				$errstr = substr($errstr, 8);
			}

			icms_loadLanguageFile('core', 'core');

			$errortext = sprintf(_CORE_PAGENOTDISPLAYED, $errstr);
			echo $errortext;
			if ($trace && function_exists('debug_backtrace')) {
				echo "<div>Backtrace:<br />";
				$trace = debug_backtrace();
				array_shift( $trace );
				foreach ( $trace as $step) {
					if (isset($step['file'])) {
						echo $this->sanitizePath($step['file']);
						echo ' (' . $step['line'] . ")\n<br />";
					}
				}
				echo '</div>';
			}
			exit();
		}
	}

	/**
	 * Sanitize path / url to file in erorr report
	 * @param  string  $path   path to sanitize
	 * @return string  $path   sanitized path
	 * @access protected
	 */
	function sanitizePath( $path) {
		$path = str_replace(
			array('\\', ICMS_ROOT_PATH, ICMS_TRUST_PATH, str_replace( '\\', '/', realpath(ICMS_ROOT_PATH))),
			array('/', '', 'TRUSTPATH', ''),
			$path
		);
		return $path;
	}

	/**
	 * Output buffering callback inserting logger dump in page output
	 * Determines wheter output can be shown (based on permissions)
	 * @param  string  $output
	 * @return string  $output
	 */
	public function render($output) {
		global $icmsModule;
		$this->addExtra('Included files', count(get_included_files()) . ' files');
		$this->addExtra(_CORE_MEMORYUSAGE, icms_conv_nr2local(icms_convert_size(memory_get_usage())) );
		$groups   = (is_object(icms::$user)) ? icms::$user->getGroups() : XOOPS_GROUP_ANONYMOUS;
		$moduleid = (isset($icmsModule) && is_object($icmsModule)) ? $icmsModule->getVar('mid') : 1;
		$gperm_handler = icms::handler('icms_member_groupperm');
		if (!$this->renderingEnabled || !$this->activated || !$gperm_handler->checkRight('enable_debug', $moduleid, $groups)) {
			return $output;
		}
		$this->renderingEnabled = $this->activated = false;
		$log = $this->dump( $this->usePopup ? 'popup' : '' );
		$pattern = '<!--{xo-logger-output}-->';
		$pos = strpos( $output, $pattern );
		if ($pos !== false) {
			return substr($output, 0, $pos) . $log . substr($output, $pos + strlen($pattern));
		} else {
			return $output . $log;
		}
	}

	/**
	 * dump the logger output
	 *
	 * @param   string  $mode
	 * @return  string  $ret
	 * @access protected
	 */
	public function dump($mode = '') {
		include ICMS_LIBRARIES_PATH . '/icms/core/Logger_render.php';
		return $ret;
	}

	/**
	 * get the current execution time of a timer
	 *
	 * @param   string  $name   name of the counter
	 * @return  float   current execution time of the counter
	 */
	public function dumpTime($name = 'ICMS') {
		if (!isset($this->logstart[$name])) {
			return 0;
		}
		$stop = isset($this->logend[$name]) ? $this->logend[$name] : $this->microtime();
		return $stop - $this->logstart[$name];
	}

	/**
	 * dumpAll
	 *
	 * @return string
	 * @deprecated	Use dump('') instead
	 * @todo	Remove in version 1.4
	 */
	public function dumpAll() {
		icms_core_Debug::setDeprecated('$this->dump("")', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
		return $this->dump( '' );
	}

	/**
	 * dumpBlocks
	 *
	 * @return unknown
	 * @deprecated	Use dump('blocks'), instead
	 * @todo	Remove in version 1.4
	 */
	public function dumpBlocks() {
		icms_core_Debug::setDeprecated('$this->dump("blocks")', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
		return $this->dump('blocks');
	}

	/**
	 * dumpExtra
	 *
	 * @return unknown
	 * @deprecated	Use dump('extra'), instead
	 * @todo	Remove in version 1.4
	 */
	public function dumpExtra() {
		icms_core_Debug::setDeprecated('$this->dump("extra")', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
		return $this->dump('extra');
	}

	/**
	 * dumpQueries
	 *
	 * @return unknown
	 * @deprecated	Use dump('queries'), instead
	 * @todo	Remove in version 1.4
	 */
	public function dumpQueries() {
		icms_core_Debug::setDeprecated('$this->dump("queries")', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
		return $this->dump('queries');
	}

	/**
	 * dumpFilters
	 *
	 * @return unknown
	 * @deprecated	Use dump('blocks'), instead
	 * @todo	Remove in version 1.4
	 */
	public function dumpFilters() {
		icms_core_Debug::setDeprecated('$this->dump("filters")', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
		return $this->dump('filters');
	}

    /**
	 * Render the list of errors
	 * This was added in 1.3 because there was a separate class - XoopsErrorHandler -
	 * having the additional class is unnecessary and the methods existed in other forms.
	 *
	 * @return   string  $list of errors
	 * @deprecated	use dump('errors'), instead
	 * @todo		Remove in version 1.4 - no occurrences in the core
	 */
	public function renderErrors() {
		icms_core_Debug::setDeprecated('$this->dump("errors")', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
		return $this->dump( 'errors' );
	}

}
