<?php

/**************************************************************
	 REGISTER EVENTSPACE VARS
 **************************************************************/

$laggerES = new Lagger_Eventspace();
$laggerES->registerReference('host', $_SERVER['HTTP_HOST']);
$laggerES->registerReference('uri', $_SERVER['REQUEST_URI']);
$laggerES->registerReference('post', $_POST);
$laggerES->registerReference('session', $_SESSION); // Session must be already started!
$laggerES->registerCallback('date', 'date', array('Y-m-d'));
$laggerES->registerCallback('time', 'date', array('H:i:s'));
$laggerES->registerCallback('microtime', 'microtime', array(true));
$laggerES->registerVar('session_id', session_id());
$laggerES->registerVar('process_id', substr(md5(mt_rand()), 25));

/**************************************************************
	 REGISTER EVENTSPACE MODIFIERS
 **************************************************************/

function varToStringLine($value) {
	return str_replace(array("\r\n", "\r", "\n"), ' ', is_scalar($value) ? $value : var_export($value, 1));
}
$laggerES->registerModifier('line', 'varToStringLine');

function quoteCSV($string) {
	return varToStringLine(str_replace(';', '\\;', $string));
}
$laggerES->registerModifier('csv', 'quoteCSV');

/**************************************************************
	 SKIPER
 **************************************************************/

$daylySkiper = new Lagger_Skiper($laggerES, SKIPER_HASH_TEMPLATE, SKIPER_EXPIRE, new Lagger_ExpireList(SKIPER_DIR, '.dayly_skiper'));

/**************************************************************
	 LAGGER INTERNAL ERRORS AND EXCEPTIONS HANDLING
 **************************************************************/

$emailAction = new Lagger_Action_Mail(ERRORS_EMAIL_FROM, ERRORS_EMAIL_TO, ERRORS_EMAIL_SUBJECT, ERRORS_EMAIL_MESSAGE);
$emailAction->setSkiper($daylySkiper, 'errors_email');

Lagger_Handler::addInternalErrorAction($emailAction);

/**************************************************************
	 DEBUG HANDLER
 **************************************************************/

$debug = new Lagger_Handler_Debug($laggerES);

function toDebug($message, $tags = null) {
	if(isset($GLOBALS['debug'])) {
		$GLOBALS['debug']->handle($message, $tags);
	}
}

if(DEBUG_STDOUT) {
	// Allows to rewrite DEBUG_STDOUT_TAGS. Try $_GET['__debug'] = 'high' or $_GET['__debug'] = ''
	$debugTagger = new Lagger_Tagger('__debug');
	
	$debug->addAction(new Lagger_Action_Print(DEBUG_STDOUT_TEMPLATE), DEBUG_STDOUT_TAGS, $debugTagger);
	// check Lagger/Action/ChromeConsole.php about how you can use it
	$debug->addAction(new Lagger_Action_ChromeConsole(dirname(__FILE__)), DEBUG_STDOUT_TAGS, $debugTagger);
}
if(DEBUG_LOGING) {
	$debug->addAction(new Lagger_Action_FileLog(DEBUG_LOGING_TEMPLATE, DEBUG_LOGING_FILEPATH, DEBUG_LOGING_LIMIT_SIZE), DEBUG_LOGING_TAGS);
}

/**************************************************************
	 ERRORS AND EXCEPTIONS HANDLERS
 **************************************************************/

$errors = new Lagger_Handler_Errors($laggerES);
$exceptions = new Lagger_Handler_Exceptions($laggerES);

if(ERRORS_STDOUT) {
	$printAction = new Lagger_Action_Print(ERRORS_STDOUT_TEMPLATE, false);
	$errors->addAction($printAction, ERRORS_STDOUT_TAGS);
	$exceptions->addAction($printAction, ERRORS_STDOUT_TAGS);
	
	// check Lagger/Action/ChromeConsole.php about how you can use it
	$errorsChromeAction = new Lagger_Action_ChromeConsole(dirname(__FILE__));
	$errors->addAction($errorsChromeAction, ERRORS_STDOUT_TAGS);
	$exceptions->addAction($errorsChromeAction, ERRORS_STDOUT_TAGS);
}

$fatalPrintAction = new Lagger_Action_Print('<br /><font color="red">Our site is FATALY dead, please check it again when we will fix it... in next summer :)', false);
$errors->addAction($fatalPrintAction, 'fatal');

if(ERRORS_LOGING) {
	$logAction = new Lagger_Action_FileLog(ERRORS_LOGING_TEMPLATE, ERRORS_LOGING_FILEPATH, ERRORS_LOGING_LIMIT_SIZE);
	$errors->addAction($logAction, ERRORS_LOGING_TAGS);
	$exceptions->addAction($logAction, ERRORS_LOGING_TAGS);
}

if(ERRORS_SMS) {
	$smsAction = new Lagger_Action_Sms(ERRORS_SMS_FROM, ERRORS_SMS_TO, ERRORS_SMS_MESSAGE, true);
	$smsAction->setSkiper($daylySkiper, 'errors_sms');
	$errors->addAction($smsAction, ERRORS_SMS_TAGS);
	$exceptions->addAction($smsAction, ERRORS_SMS_TAGS);
}

if(ERRORS_EMAIL) {
	$errors->addAction($emailAction, ERRORS_EMAIL_TAGS);
	$exceptions->addAction($emailAction, ERRORS_EMAIL_TAGS);
}