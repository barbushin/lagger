<?php

class Lagger_Action_WinSpeak extends Lagger_Action {
	
	protected $spVoice;
	protected $textTamplate;
	
	public function __construct($textTemplate, $volume=100, $rate=0) {
		$this->spVoice = new COM('SAPI.SpVoice');
		$this->spVoice->Rate = $rate;
		$this->spVoice->Volume = $volume;
		
		$this->textTemplate = $textTemplate;
	}
	
	protected function make() {
		$this->spVoice->Speak($this->eventspace->fetch($this->textTemplate));
	}
}