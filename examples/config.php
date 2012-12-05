<?php

// USING CONSTANTS IS NOT REQUIRED!
// CHECK lagger_init.php ABOUT HOW LAGGER IS CONFIGURED


define('LOGS_DIR', 'logs');

define('SKIPER_DIR', LOGS_DIR . DIRECTORY_SEPARATOR . 'skip');
define('SKIPER_EXPIRE', 60 * 60 * 24);
define('SKIPER_HASH_TEMPLATE', '{file}{line}');

define('ERRORS_STDOUT', true);
define('ERRORS_STDOUT_TAGS', null);
define('ERRORS_STDOUT_TEMPLATE', '<div><font color="red"><b>{type}:</b> {message}<br /><em>{file} [{line}]</em></font></div>');

define('ERRORS_LOGING', true);
define('ERRORS_LOGING_TAGS', 'warning,fatal');
define('ERRORS_LOGING_FILEPATH', LOGS_DIR . DIRECTORY_SEPARATOR . 'errors_log.htm');
define('ERRORS_LOGING_LIMIT_SIZE', 500000);
define('ERRORS_LOGING_TEMPLATE', '{date} {time} <a href="http://{host}{uri}">http://{host}{uri}</a><br /><b>{type}</b>: {message|htmlentities}<br />{file} [{line}]<br />{trace|htmlentities|nl2br}<hr />');

define('ERRORS_SMS', false); // check /library/SmsSender.php before enable it
define('ERRORS_SMS_TAGS', 'warning,fatal');
define('ERRORS_SMS_TO', '79627271169,79218550471');
define('ERRORS_SMS_FROM', 'MyWebSite');
define('ERRORS_SMS_MESSAGE', 'Web site error, check log at {date} {time}');

define('ERRORS_EMAIL', true);
define('ERRORS_EMAIL_TAGS', 'warning,fatal');
define('ERRORS_EMAIL_FROM', 'Lagger <lagger@mywebsite.com>');
define('ERRORS_EMAIL_TO', 'Jack Johnson <jack_admin@gmail.com>, mike_developer@gmail.com');
define('ERRORS_EMAIL_SUBJECT', '{type} error in my website');
define('ERRORS_EMAIL_MESSAGE', "Date: {date} {time}\nURL: http://{host}{uri}\nError({type}): {message}\nSource: {file} [{line}]\n\nTrace:\n{trace}\n\nPOST:\n{post}\n\nSESSION:\n{session}");

define('DEBUG_STDOUT', true);
define('DEBUG_STDOUT_TAGS', 'test,high');
define('DEBUG_STDOUT_TEMPLATE', '<div><font color="green">{message|htmlentities}</font></div>');

define('DEBUG_LOGING', true);
define('DEBUG_LOGING_TAGS', 'sql');
define('DEBUG_LOGING_FILEPATH', LOGS_DIR . DIRECTORY_SEPARATOR . 'debug_sql_log.csv');
define('DEBUG_LOGING_LIMIT_SIZE', 500000);
define('DEBUG_LOGING_TEMPLATE', "{date} {time};{process_id|csv};{microtime|csv};{tags|csv};{message|trim|csv}");

// Autoload Lagger classes (check alternative way in /examples/build_phar/)
define('LIB_DIR', dirname(dirname(__FILE__)) . '/library/');
function autoloadByDir($class) {
	$filePath = LIB_DIR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
	require_once ($filePath);
}
spl_autoload_register('autoloadByDir');