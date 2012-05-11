<?php
ini_set("soap.wsdl_cache_enabled", "0"); 
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
	

//set_include_path(APPLICATION_PATH . '/../ZendFramework/library' . PATH_SEPARATOR . get_include_path());


define('ZENDLIB_NEW',"../../ZendFramework/library/");
set_include_path(ZENDLIB_NEW);

require_once('../_inc/config.inc');
require_once('Zend/Soap/AutoDiscover.php');
require_once('Zend/Soap/Server.php');
require_once('Zend/Soap/Wsdl.php');
require_once('commentServices.php');
try{
$wsdl = new Zend_Soap_Autodiscover();
//print_r($wsdl);
$wsdl->setClass('commentServices');
if (isset($_GET['wsdl'])) {
$wsdl->handle();
    } else {
    $server = new Zend_Soap_Server('http://qa.lighthouse.nbcuots.com/services/saveComment.php?wsdl');
    $server->setClass('commentServices');
    $server->setEncoding('ISO-8859-1');
    $server->handle();

    }

}catch(SoapFault $e){

echo $e->getMessage();
}

/*$wsdl->setClass('commentServices');
if (isset($_GET['wsdl'])) {
$wsdl->handle();
    } else {
    $server = new Zend_Soap_Server('http://dev3.lighthouse.nbcuots.com/services/saveComment.php?wsdl');
    $server->setClass('commentServices');
    $server->setEncoding('ISO-8859-1');
    $server->handle();
   
    }
*/
