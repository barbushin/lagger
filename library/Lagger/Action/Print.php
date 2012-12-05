<?php

/**
 * 
 * @see http://code.google.com/p/lagger
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */
class Lagger_Action_Print extends Lagger_Action{
	
	protected $template;
	protected $buffering;
	
	protected static $buffer;

	public function __construct($template, $buffering = false, $flushBufferOnExit = true) {
		$this->template = $template;
		$this->buffering = $buffering;
		
		if($flushBufferOnExit) {
			register_shutdown_function(array('Lagger_Action_Print', 'flush'));
		}
	}

	public function startBuffering() {
		$this->buffering = true;
	}

	public function stopBuffering() {
		$this->buffering = false;
	}

	protected function make() {
		if ($this->buffering) {
			self::$buffer[] = $this->eventspace->fetch($this->template);
		}
		else {
			self::show($this->eventspace->fetch($this->template));
		}
	}

	public static function flush($return=false) {
		if (self::$buffer) {
			$outputString = implode(' ', self::$buffer);
			self::$buffer = array();
			if($return) {
				return $outputString;
			}
			else {
				self::show($outputString);
			}
		}
	}

	protected static function show($string) {
		echo $string;
	}
	
	public static function flushToHtmlBody($html, $return=false) {
		if(preg_match('/<body.*?>/i', $html)) {
			$result = preg_replace('/(<body.*?>)/i', '\\1'.self::flush(true), $html, 1);
		}
		else {
			$result = self::flush(true).$html;
		}
		if($return) {
			return $result;
		}
		else {
			self::show($result);
		}
	}
}