<?php

/**
 * 
 * @see http://code.google.com/p/lagger
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */
abstract class Lagger_Action {
	
	protected $skiper;
	protected $skiperGroup;
	
	/**
	 * @var Lagger_Eventspace
	 */
	protected $eventspace;

	public function callMake(Lagger_Eventspace $eventspace) {
		$this->eventspace = $eventspace;
		
		if ($this->skiper) {
			if (!$this->skiper->isSkiped($this->skiperGroup)) {
				$this->skiper->setSkip($this->skiperGroup);
				$this->make();
			}
		}
		else {
			$this->make();
		}
	}
	
	public function setSkiper(Lagger_Skiper $skiper, $skiperGroup = null) {
		$this->skiper = $skiper;
		$this->skiperGroup = $skiperGroup ? $skiperGroup . '_' : null;
	}

	abstract protected function make();
}
