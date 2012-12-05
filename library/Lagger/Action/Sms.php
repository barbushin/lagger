<?php

/**
 * 
 * @see http://code.google.com/p/lagger
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */
class Lagger_Action_Sms extends Lagger_Action{
	
	protected $from;
	protected $to = array();
	protected $translit;
	protected $messageTemplate;

	public function __construct($from, $to, $messageTemplate, $translit = true) {
		$this->from = $from;
		$this->to = is_array($to) ? $to : explode(',', $to);
		$this->messageTemplate = $messageTemplate;
		$this->translit = $translit;
	}

	protected function make() {
		foreach ($this->to as $to) {
			$this->sendSms($this->from, trim($to), $this->eventspace->fetch($this->messageTemplate), $this->translit);
		}
	}

	protected function sendSms($from, $to, $message, $translit=false) {
		$smsSender = new SmsSender();
		$smsSender->send($from, $to, $message, $translit);
	}
}
