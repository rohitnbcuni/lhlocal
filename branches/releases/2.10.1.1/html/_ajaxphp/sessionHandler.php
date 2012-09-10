<?php

@session_start();

if(!ISSET($_SESSION['user_id'])){
	die("<b>You are not allowed to access these files</b>");
}

$ext = array('SELECT','select','Select','UPDATE','Update','INSERT','Insert','insert','show Tables','show tables','SHOW TABLES','DATABASES','DELETE','Delete','TABLES');


$pageURL = 'http';
if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
$pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
// echo $pageURL;

 foreach ($ext as &$value) {
    //echo $value.'<BR/>';
    $str= explode($value,$pageURL);

        if(count($str)>1)
    {die('<center><b style="margin-top: 200px; font-family: fantasy;">Wrong parameter provided</b></center>');}
}




