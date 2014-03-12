<?php

    include('../_inc/config.inc');
	include 'reader.php';
	global $mysql;
    //$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);	
	exit;

    // initialize reader object
   /* $excel = new Spreadsheet_Excel_Reader();
    
    // read spreadsheet data
    $excel->read('LH_ProjectList_Programs.xls');    
    
    // iterate over spreadsheet cells and print as HTML table
    $x=2;
    while($x<=$excel->sheets[0]['numRows']) {
	$sql="UPDATE projects SET program = ".$excel->sheets[0]['cells'][$x][4]." where id = ".$excel->sheets[0]['cells'][$x][3]."";
	echo ($x-1)." ".$sql."<br>";
	++$x; 

   $mysql->sqlordie($sql);
   }
   echo "<br><br>end of script";*/
?>    