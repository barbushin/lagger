<?php

/**
 *
 * @see http://code.google.com/p/lagger
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 *
 */
abstract class Lagger_Handler {

	protected $eventspace;
	protected $actions = array();
	protected $currentAction;
	protected $handling;
	public static $skipNexInternalException;
	protected static $internalErrorsActions = array();

	const tagSeparator = ',';

	public function __construct(Lagger_Eventspace $eventspace) {
		$this->eventspace = $eventspace;
		$this->init();
	}

	protected function init() {
	}

	public function getEventspace() {
		return $this->eventspace;
	}

	public function addAction(Lagger_Action $action, $tags = null, Lagger_Tagger $tagger = null) {
		if($tagger && $tagger->tagsRewrited()) {
			$tags = $tagger->getNewTags();
		}
		if($tags === '') {
			$tags = null;
		}
		if($tags || $tags === null) {
			$incTags = null;
			$excTags = null;
			self::parseActionTagsString($tags, $incTags, $excTags);
			$this->actions[] = array('object' => $action, 'included_tags' => $incTags, 'excluded_tags' => $excTags);
		}
		return $this;
	}

	protected static function convertTraceToString($traceData, $eventFile = null, $eventLine = null) {
		$trace = array();
		foreach($traceData as $i => $call) {
			if((isset($call['class']) && strpos($call['class'], 'Lagger_') === 0) || (!$trace && isset($call['file']) && $call['file'] == $eventFile && $call['line'] == $eventLine)) {
				$trace = array();
				continue;
			}
			$args = array();
			if(isset($call['args'])) {
				foreach($call['args'] as $arg) {
					if(is_object($arg)) {
						$args[] = get_class($arg);
					}
					elseif(is_array($arg)) {
						$args[] = 'Array';
					}
					else {
						$arg = var_export($arg, 1);
						$args[] = strlen($arg) > 12 ? substr($arg, 0, 8) . '...\'' : $arg;
					}
				}
			}
			$trace[] = (isset($call['file']) ? ($call['file'] . ':' . $call['line']) : '[internal call]') . ' - ' . (isset($call['class']) ? $call['class'] . $call['type'] : '') . $call['function'] . '(' . implode(', ', $args) . ')';
		}
		$trace = array_reverse($trace);
		foreach($trace as $i => &$call) {
			$call = '#' . ($i + 1) . ' ' . $call;
		}
		return implode("\n", $trace);
	}

	protected static function parseActionTagsString($tagsString, &$incTags, &$excTags = array()) {
		if(preg_match_all('/(-(\w+))|(\w+)/', $tagsString, $matches)) {
			foreach($matches[3] as $i => $incTag) {
				if($incTag === '') {
					$excTags[] = $matches[2][$i];
				}
				else {
					$incTags[] = $incTag;
				}
			}
		}
	}

	protected static function parseEventTagsString($tagsString) {
		return array_map('trim', explode(self::tagSeparator, $tagsString));
	}

	protected function handleActions(array $eventVars, $eventTags = null) {
		if(!$this->handling) { // TODO: require some handler for internal Lagger errors
			$this->handling = true;
			$eventVars['tags'] = $eventTags;
			if(!isset($eventVars['handler'])) {
				$eventVars['handler'] = get_class($this);
			}
			$this->eventspace->resetVarsValues($eventVars);
			$throwException = null;
			foreach($this->getActionsByTags($eventTags) as $action) {
				try {
					$this->currentAction = $action['object'];
					$action['object']->callMake($this->eventspace);
				}
				catch(Exception $e) {
					if(self::$skipNexInternalException) {
						self::$skipNexInternalException = false;
						$throwException = $e;
					}
					else {
						self::handleInternalError($this->eventspace, get_class($e), 'There is internal error during handling "' . get_class($this->currentAction) . '": ' . print_r($e, true));
					}
				}
			}
			$this->handling = false;
			if($throwException) {
				throw $throwException;
			}
		}
	}

	protected function getActionsByTags($eventTagsString) {
		$actions = array();
		$eventTags = self::parseEventTagsString($eventTagsString);
		foreach($this->actions as $action) {
			if($this->isTagsMatches($eventTags, $action['included_tags'], $action['excluded_tags'])) {
				$actions[] = $action;
			}
		}
		return $actions;
	}

	protected function isTagsMatches($eventTags, $incTags, $excTags) {
		return (!$excTags || !array_intersect($eventTags, $excTags)) && (!$incTags || array_intersect($incTags, $eventTags));
	}

	/**************************************************************
	INTERNAL ERROR HANDLING
	 **************************************************************/

	public static function addInternalErrorAction(Lagger_Action $action) {
		self::$internalErrorsActions[] = $action;
	}

	protected static function handleInternalError(Lagger_Eventspace $eventspace, $type, $message) {
		$newEventspace = clone $eventspace;
		$eventVars = array('message' => $message, 'type' => $type);
		$newEventspace->resetVarsValues($eventVars);
		foreach(self::$internalErrorsActions as $action) {
			$action->callMake($newEventspace);
		}
	}

	public function __destruct() {
		if($this->handling) {
			self::handleInternalError($this->eventspace, 'LAGGER_INTERNAL_FATAL', 'Unkown internal FATAL error in handling "' . get_class($this->currentAction) . '"');
		}
	}
}
