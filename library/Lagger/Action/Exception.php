<?php

/**
 * 
 * @see https://github.com/barbushin/lagger
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 * 
 */
class Lagger_Action_Exception extends Lagger_Action{

	protected $messageTemplate;
	
	public function __construct($messageTemplate = null) {
		$this->messageTemplate = $messageTemplate ? $messageTemplate : '{message}';
	}

	protected function make() {
		Lagger_Handler::$skipNexInternalException = true;
		throw new ErrorException($this->eventspace->fetch($this->messageTemplate), (int)$this->eventspace->code, 0, $this->eventspace->file, $this->eventspace->line);
	}
}