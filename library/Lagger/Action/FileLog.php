<?php

/**
 *
 * @see http://code.google.com/p/lagger
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 *
 */
class Lagger_Action_FileLog extends Lagger_Action {

	protected $template;
	protected $filepath;
	protected $chmod;
	protected $sizeLimit;

	const checkLimit = 100;

	public function __construct($template, $filepath, $sizeLimit = null, $chmod = 0666) {
		if(!file_exists($filepath)) {
			$this->reinitLogFile($filepath, $chmod);
		}
		$this->template = $template;
		$this->filepath = realpath($filepath); // realpath is required for fopen works on script shutdown
		$this->chmod = $chmod;
		$this->sizeLimit = (int) $sizeLimit;
	}

	protected function reinitLogFile($filepath, $chmod = null) {
		file_put_contents($filepath, '');
		if($chmod && strpos(PHP_OS, 'WIN') === false) {
			chmod($filepath, $chmod);
		}
	}

	protected function make() {
		$this->checkLimits();
		$logString = $this->eventspace->fetch($this->template) . "\n";
		$fp = fopen($this->filepath, 'a');
		fputs($fp, $logString);
		fclose($fp);
	}

	protected function checkLimits() {
		if($this->sizeLimit && !mt_rand(0, self::checkLimit) && filesize($this->filepath) > $this->sizeLimit) {
			$this->reinitLogFile($this->filepath, $this->chmod);
		}
	}
}
