<?php

/**
 * 
 * @see http://code.google.com/p/lagger
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */
class Lagger_Tagger {
	
	protected $newTags;
	const sessionVar = 'lagger';
	const tagsResetVar = '__reset';

	public function __construct($tagsVar, $secret = null, $secretVar = '__pin') {
		$this->newTags = $this->checkRewriteTags($tagsVar, $secret, $secretVar);
	}

	public function tagsRewrited() {
		return $this->newTags !== null;
	}

	public function getNewTags() {
		return $this->newTags;
	}

	protected function getRewriteSession() {
		if (isset($_COOKIE[session_name()])) {
			if(!session_id()) {
				session_start();
			}
			return isset($_SESSION[self::sessionVar]) ? $_SESSION[self::sessionVar] : null;
		}
	}

	protected function setRewriteSession($sessionData) {
		if (!session_id()) {
			session_start();
		}
		$_SESSION[self::sessionVar] = $sessionData;
	}

	protected function checkRewriteTags($tagsVar, $secret, $secretVar) {
		$rewriteKey = $tagsVar . $secretVar . $secret;
		$sessionData = $this->getRewriteSession();
		if (isset($_GET[$tagsVar]) && (!$secret || (isset($_GET[$secretVar]) && $_GET[$secretVar] == $secret))) {
			$sessionData[$rewriteKey] = $_GET[$tagsVar];
			$this->setRewriteSession($sessionData);
		}
		elseif (isset($_GET[self::tagsResetVar])) {
			$this->setRewriteSession(array());
			return null;
		}
		
		if (isset($sessionData[$rewriteKey])) {
			return $sessionData[$rewriteKey];
		}
		return null;
	}
}
