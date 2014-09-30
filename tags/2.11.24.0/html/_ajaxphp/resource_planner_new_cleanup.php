<?php	

		include('../_inc/config.inc');
        include("sessionHandler.php");
        //$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, 'lhdev_live2' , DB_PORT);
		 //  $mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		exit;
		     
        $resource_all = "SELECT * FROM `resource_blocks` WHERE `projectid` > '14699' AND status = '3' AND YEAR(datestamp) ='2012' AND month(datestamp)='01'";
        $resource_list = $mysql->sqlordie($resource_all);
        
        if($resource_list->num_rows > 0) {
                echo "<center> <b> Resource Planner cleanup<b></center>";
                echo "<br>List of Resource Data : ";
                echo "<br>----------------------------------------------------------<br>";
                print "<pre>";
                $i=0;
                while($row1 = $resource_list->fetch_assoc()) {
                        $duplicate_resource = "SELECT * FROM `resource_blocks` WHERE   userid ='".$row1['userid']."' AND
                        projectid ='".$row1['projectid']."' AND daypart ='".$row1['daypart']."' AND datestamp ='".$row1['datestamp']."' AND id <> '".$row1['id']."' ";
                        $duplicate_resource_list = $mysql->sqlordie($duplicate_resource);
                        if($duplicate_resource_list->num_rows > 0) {
                                        while($row2 = $duplicate_resource_list->fetch_assoc()) {
                                                echo "Update record id :".$row1['id']."-----New exist id : ".$row2['id'];
                                                print_r($row2);
                                                $update_sql = "UPDATE  `resource_blocks` SET status='0' ,daypart='0', datestamp='0',dateadded='0' where id ='".$row1['id']."'";
                                                $mysql->sqlordie($update_sql);
                                                echo "<br/>";
                                        }
                                }
                        }

                }

		
?>
