<?php

/**
 *
 * @see http://code.google.com/p/lagger
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 *
 */
class Lagger_Handler_Errors extends Lagger_Handler {

	protected static $codesTags = array(E_ERROR => 'fatal', E_WARNING => 'warning', E_PARSE => 'fatal', E_NOTICE => 'notice', E_CORE_ERROR => 'fatal', E_CORE_WARNING => 'warning', E_COMPILE_ERROR => 'fatal', E_COMPILE_WARNING => 'warning', E_USER_ERROR => 'fatal', E_USER_WARNING => 'warning', E_USER_NOTICE => 'notice', E_STRICT => 'warning');
	protected static $codesNames = array(E_ERROR => 'E_ERROR', E_WARNING => 'E_WARNING', E_PARSE => 'E_PARSE', E_NOTICE => 'E_NOTICE', E_CORE_ERROR => 'E_CORE_ERROR', E_CORE_WARNING => 'E_CORE_WARNING', E_COMPILE_ERROR => 'E_COMPILE_ERROR', E_COMPILE_WARNING => 'E_COMPILE_WARNING', E_USER_ERROR => 'E_USER_ERROR', E_USER_WARNING => 'E_USER_WARNING', E_USER_NOTICE => 'E_USER_NOTICE', E_STRICT => 'E_STRICT');
	protected static $notCompitableCodes = array('E_RECOVERABLE_ERROR' => 'warning', 'E_DEPRECATED' => 'warning');

	protected $iniSets = array('display_errors' => false, 'html_errors' => false, 'ignore_repeated_errors' => false, 'ignore_repeated_source' => false);
	protected $oldErrorHandler;
	protected $callOldErrorHandler;

	public function __construct(Lagger_Eventspace $eventspace, $callOldErrorHandler = false, $htmlErrors = false, $ignoreRepeatedErrors = false, $ignoreRepeatedSource = false) {
		$this->callOldErrorHandler = $callOldErrorHandler;
		$this->iniSets['html_errors'] = $htmlErrors;
		$this->iniSets['ignore_repeated_errors'] = $ignoreRepeatedErrors;
		$this->iniSets['ignore_repeated_source'] = $ignoreRepeatedSource;
		foreach(self::$notCompitableCodes as $code => $tag) {
			if(defined($code)) {
				self::$codesTags[constant($code)] = $tag;
				self::$codesNames[constant($code)] = $code;
			}
		}
		parent::__construct($eventspace);
	}

	protected function init() {
		foreach($this->iniSets as $attribute => $value) {
			ini_set($attribute, $value);
		}
		$this->oldErrorHandler = set_error_handler(array($this, 'handle'));
		register_shutdown_function(array($this, 'checkFatalError'));
	}

	public function checkFatalError() {
		$error = error_get_last();
		if($error) {
			$this->handle($error['type'], $error['message'], $error['file'], $error['line']);
		}
	}

	public function handle($code = null, $message = null, $file = null, $line = null, $customTags = null) {
		if(error_reporting() == 0) { // if error has been supressed with an @
			return;
		}
		if(!$code) {
			$code = E_USER_ERROR;
		}

		$eventTags = 'error,' . (isset(self::$codesTags[$code]) ? self::$codesTags[$code] : 'warning') . ($customTags ? ',' . $customTags : '');
		$eventVars = array('message' => $message, 'code' => $code, 'type' => isset(self::$codesNames[$code]) ? self::$codesNames[$code] : $code, 'file' => $file, 'line' => $line);

		$traceData = debug_backtrace();
		if($traceData) {
			$eventVars['trace'] = self::convertTraceToString($traceData, $file, $line);
		}

		$this->handleActions($eventVars, $eventTags);

		if($this->callOldErrorHandler && $this->oldErrorHandler) {
			call_user_func_array($this->oldErrorHandler, array($code, $message, $file, $line));
		}
	}

	public function __destruct() {
		if($this->oldErrorHandler) {
			set_error_handler($this->oldErrorHandler);
		}
	}
}
