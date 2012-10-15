<?php
  include('../_inc/config.inc');
  $mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	$daypart = "";


	if($_REQUEST["daypart"]){
		$daypart = $_REQUEST["daypart"];
	}else{
		$daypart =  $argv[1];
	}

  echo "<center> <b> Resource Planner Daypart To Hours<b></center>";

  if($daypart == "4"){	
	  $overtime_daypart_query = "UPDATE `resource_blocks` SET `daypart`='9' WHERE `daypart`='5'";  
	  $overtime_daypart = $mysql->query($overtime_daypart_query);	
  }
  
  //$all_daypart_query = "SELECT * FROM `resource_blocks` WHERE `daypart` in ('1', '2', '3', '4') order by `daypart` DESC ";
  $all_daypart_query = "SELECT * FROM `resource_blocks` WHERE `daypart` = '" . $daypart . "' order by `daypart` DESC ";
   
  $all_daypart = $mysql->query($all_daypart_query);
  
  if(@$all_daypart->num_rows > 0) {
  
    while($row = @$all_daypart->fetch_assoc()) {
  
      $daypart = $row['daypart'];
      $daypart1 = $daypart*2;
      $daypart2 = ($daypart*2)-1;
      	
      $update_daypart_query = "UPDATE `resource_blocks` SET `daypart`= ".$daypart1." WHERE `id`= ".$row['id'];
  
      $mysql->query($update_daypart_query);
  
      $insert_daypart_query = 'INSERT INTO `resource_blocks` (`userid`,`projectid`,`daypart`,`status`,`datestamp`,`dateadded`,`active`,`deleted`)VALUES ( "'.$row['userid'].'", "'.$row['projectid'].'", "'.$daypart2.'","'.$row['status'].'","'.$row['datestamp'].'","'.$row['dateadded'].'","'.$row['active'].'","'.$row['deleted'].'")';
      	
      $mysql->query($insert_daypart_query);
  
    }
  }
?>