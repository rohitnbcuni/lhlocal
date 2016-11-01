<?PHP
error_reporting(0);

if($_SERVER['REQUEST_URI'] == '/favicon.ico'){
	// To stop any process for this request.
	die();
}
	/*
	Creator: Jandaco
	Date:  Oct. 12, 2008
	Developer: Justin Ishoy
	Project: NBC Lighthouse
	Desc:
	Zend bootstrap file for Project Lighthouse - This file is the standard Zend bootstrap file.
	*/
	//session_start();
	
	// ww
	
	define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
	
	
	//set_include_path(APPLICATION_PATH . '/../ZendFramework/library' . PATH_SEPARATOR . get_include_path());
	
	define('AJAX_CALL', '0');
	include('_inc/config.inc');
	
	set_include_path(ZENDLIB . PATH_SEPARATOR . APPLIB . PATH_SEPARATOR .APPPATH . PATH_SEPARATOR . WEBPATH . PATH_SEPARATOR . FCKPATH . PATH_SEPARATOR . get_include_path());
	

	require_once "Zend/Loader.php";
	Zend_Loader::registerAutoload();

	//Load Zend library loader and base controller
	require_once('Zend/Loader.php');
	require_once('Zend/Config/Ini.php');
	require_once('Zend/Session.php');
	require_once('fpdf.inc');
	require('basecamp.inc');
	//require('phpBasecamp.php');
	
	
	
	//Resource planner
	//require_once('/var/www/tools/application/models/loadModel.php');
	
	$app = '';	
	//Setup Zend base framework
	try {
		require('bootstrap.php');
		$app = new LighthouseApp();
		$app->init();
	} catch(Exception $exp) {
		//Echo the errors for the bootstrap
		$contentType = 'text/html';
		
		header("Content-Type: $contentType; charse=utf-8");
		echo 'an unexpected error occurred. Cannon load bootstrap or create object in index.php';
		echo '<h3>Unexpected Exception: ' .$exp->getMessage() .'</h3><br /><pre>';
		echo $exp->getTraceAsString();
	}
?>
