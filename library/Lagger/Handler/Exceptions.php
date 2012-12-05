<?php

/**
 *
 * @see http://code.google.com/p/lagger
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 *
 */
class Lagger_Handler_Exceptions extends Lagger_Handler {

	protected $oldExceptionsHandler;
	protected $callOldExceptionsHandler;

	public function __construct(Lagger_Eventspace $eventspace, $callOldExceptionsHandler=false) {
		$this->callOldExceptionsHandler = $callOldExceptionsHandler;
		parent::__construct($eventspace);
	}

	protected function init() {
		$this->oldExceptionsHandler = set_exception_handler(array($this, 'handle'));
	}

	public function handle(Exception $exception) {
		$code = $exception->getCode() ? $exception->getCode() : E_USER_ERROR;

		$eventTags = 'error,exception,fatal,'.get_class($exception);
		$eventVars = array(
		'message' => $exception->getMessage(),
		'code' => $code,
		'type' => get_class($exception),
		'file' => $exception->getFile(),
		'line' => $exception->getLine(),
		'exception' => $exception);

		if($exception->getTrace()) {
			$eventVars['trace'] = self::convertTraceToString($exception->getTrace(), $eventVars['file'], $eventVars['line']);
		}

		$this->handleActions($eventVars, $eventTags);

		if ($this->oldExceptionsHandler && $this->callOldExceptionsHandler) {
			call_user_func_array($this->oldExceptionsHandler, array($exception));
		}
	}

	public function __destruct() {
		if ($this->oldExceptionsHandler) {
			set_exception_handler($this->oldExceptionsHandler);
		}
	}
}