<?php

/**
 * 
 * @see http://code.google.com/p/lagger
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */
class Lagger_Skiper {
	
	protected $eventspace;
	protected $hashTemplate;
	protected $expire;
	protected $expireList;

	public function __construct(Lagger_Eventspace $eventspace, $hashTemplate, $expireInSeconds, Lagger_ExpireList $expireList) {
		$this->eventspace = $eventspace;
		$this->hashTemplate = $hashTemplate;
		$this->expire = $expireInSeconds;
		$this->expireList = $expireList;
	}

	public function isSkiped($skiperGroup = null) {
		return !$this->expireList->isExpired(md5($this->eventspace->fetch($this->hashTemplate)), $skiperGroup);
	}

	public function setSkip($skiperGroup = null) {
		if ($this->expire) {
			return $this->expireList->setExpire(md5($this->eventspace->fetch($this->hashTemplate)), $this->expire, $skiperGroup);
		}
	}

	public function reset() {
		$this->expireList->clearAll();
	}
}