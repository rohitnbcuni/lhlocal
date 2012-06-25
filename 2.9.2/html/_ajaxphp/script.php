<?php

   include('../_inc/config.inc');
	include 'reader.php';
    $mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);	

    // include class file
  
    
    // initialize reader object
    $excel = new Spreadsheet_Excel_Reader();
    
    // read spreadsheet data
    $excel->read('LH_Users_Upload.xls');    
    
    // iterate over spreadsheet cells and print as HTML table
    $x=1;
    while($x<=$excel->sheets[0]['numRows']) {
      
    $x++;
	echo $x;
    $sql="UPDATE users SET program = '".$excel->sheets[0]['cells'][$x][3]."' , agency = '".$excel->sheets[0]['cells'][$x][7]."' , active = '".$excel->sheets[0]['cells'][$x][8]."' , deleted = '".$excel->sheets[0]['cells'][$x][9]."' where id = ".$excel->sheets[0]['cells'][$x][1].";";
    echo $sql."<br>";

   $mysql->query($sql);
   }
   echo "end of script";
?>    

