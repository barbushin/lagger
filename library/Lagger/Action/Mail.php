<?php

/**
 * 
 * @see http://code.google.com/p/lagger
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */
class Lagger_Action_Mail extends Lagger_Action{
	
	protected $from;
	protected $to;
	protected $subjectTemplate;
	protected $bodyTemplate;

	public function __construct($from, $to, $subjectTemplate, $bodyTemplate) {
		$this->from = $from;
		$this->to = is_string($to) ? explode(',', $to) : $to;
		$this->subjectTemplate = $subjectTemplate;
		$this->bodyTemplate = $bodyTemplate;
	}

	protected function make() {
		foreach ($this->to as $to) {
			$this->sendMail($this->from, $to, $this->eventspace->fetch($this->subjectTemplate), $this->eventspace->fetch($this->bodyTemplate));
		}
	}

	protected function sendMail($from, $to, $subject, $message) {
		mail($to, $subject, $message, 'From: '.$from);
	}
}
