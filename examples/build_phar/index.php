<?php

/**
 *
 * @desc There is example of creating PHAR archive of Lagger, so now it can be included in your project just like: require_once('phar://'.LAGGER_PHAR_FILEPATH);
 * @see https://github.com/barbushin/lagger
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 *
 */

require_once ('../config.php');

define('LAGGER_DIR', LIB_DIR . 'Lagger');
define('LAGGER_PHAR_FILEPATH', dirname(__FILE__) . '/Lagger.phar');

if(!Phar::canWrite()) {
	throw new Exception('Unable to create PHAR archive, must be phar.readonly=Off option in php.ini');
}
if(file_exists(LAGGER_PHAR_FILEPATH)) {
	unlink(LAGGER_PHAR_FILEPATH);
}

$phar = new Phar(LAGGER_PHAR_FILEPATH);
$phar = $phar->convertToExecutable(Phar::PHAR);
$phar->startBuffering();
$phar->buildFromDirectory(LAGGER_DIR, '/\.php$/');
$phar->setStub('<?php

Phar::mapPhar("Lagger");
function autoloadLaggerByDir($class) {
	if(strpos($class, "Lagger_") === 0) {
		require_once("phar://" . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php");
	}
}
spl_autoload_register("autoloadLaggerByDir");
__HALT_COMPILER();

');
$phar->stopBuffering();

?>
<pre>
Done. See <?= LAGGER_PHAR_FILEPATH ?>
Now you can include Lagger to your project just by:

require_once('phar://<?= LAGGER_PHAR_FILEPATH ?>);