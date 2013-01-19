require_once 'FirePHPCore/FirePHP.class.php';

ob_start();

$firephp = FirePHP::getInstance(true);
$var = "Message 1";

$firephp->log($var);

