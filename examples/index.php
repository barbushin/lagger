<?php

require_once ('config.php');
require_once ('lagger_init.php');


echo '<h3>Simple debug messages (default tag is "debug"). <br />Tags output is configured in: define("DEBUG_STDOUT_TAGS", "test,high")</h3>'; 

toDebug('Debug message with default tag "debug"'); // will be not printed
toDebug('Debug message with tag "high"', 'high'); // will be not printed
toDebug('Debug message with tags "high,test"', 'high,test'); // will be printed
toDebug('Debug message with tags "high,test,database"', 'high,test,database'); // will be printed

echo '<br /><b>You can override tags for debug output by __debug parameter in GET:</b><br />
<a href="?__debug=">Show all</a><br />
<a href="?__debug=high,test,database">Show only tags "high,test,database"</a><br />
<a href="?__debug=high,-database">Show "high" tags and exclude "database" tags</a><br />
<a href="?__reset">Reset to config settings</a><br />'; 

// Debug by tag 'sql'. Check output in '\examples\logs\debug_sql_log.csv'
$sql = 'SELECT * FROM users';
toDebug($sql, 'sql');

echo '<h3>Some PHP errors</h3>'; 
$blahamuha = $some['unkownVar'];
file_get_contents('blahamuha.txt');


class TestBacktrace {
	public function __construct() {
		$this->f1(true, null);
	}
	public function f1() {
		$this->f2(array());
	}
	public function f2() {
		self::f3(new StdClass());
	}
	public static function f3() {
		echo $someUnkownVar;
		throw new Exception('There is some exception');
	}
}

echo '<h3>If you catch all exceptions to show user error page, so you should do it like this</h3>'; 
try {
	$obj = new TestBacktrace('some string');
}
catch (Exception $e) {
	$exceptions->handle($e);
	echo 'Hi, user! <br />There is some problem with our server, check it up tomorrow or better in next summer';
}

echo '<h3>You can set Lagger to generate Exceptions on PHP errors</h3>';
$errors->addAction(new Lagger_Action_Exception(), 'notice,warning,fatal');
try {
	file_get_contents('blahamuha.txt'); // some E_WARNING PHP error
}
catch (ErrorException $e) {
	$exceptions->handle($e);
}

echo '<h3>But otherwise not catched exceptions will break the script</h3>';
unkown_function('blahamuha kaput'); // some fatal error
echo 'So, this text will be never printed, but otherwise fatal error message will be in log file';