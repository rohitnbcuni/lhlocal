<?php
$folder = './';
$src = isset($_GET['files'])?$_GET['files']:"";
$out = 'myScript.js';
$output = "";

if($src!="") {
	require 'class.JavaScriptPacker.php';
	$files = explode(',', $src);
	for($f=0;$f<count($files);$f++){
		$script = file_get_contents($folder.$files[$f]);
		$t1 = microtime(true);
		$packer = new JavaScriptPacker($script, 'Normal', true, false);
		$output .= $packer->pack() . "\r\n";
	}
	$t2 = microtime(true);
	$time = sprintf('%.4f', ($t2 - $t1) );
	echo $output;
}
?>
