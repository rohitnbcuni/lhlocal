<?PHP
	include('../_inc/config.inc');
    $mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);	
   
	$proj_arr = array('ATV' => 14182
, 'Content Intelligence' => 12002
, 'Identity' => 14180
,'Mobile' => 14181
, 'Operations' => 12005
 , 'Publishing' => 11996
, 'Revenue Enablers' => '' ,'Site Solutions' => 14185
,'Social Media'=> '','Video'=> 12000
);

	$user_sql = "SELECT a.id,b.`program` FROM `users` a,`lnk_programs` b where a.program = b.id AND a.`id` IN(263,86,273,1765,1767,125,1060,1738,1745,936,56,295,1501,1096,21,1577,1678,6,1628,589,586,1325,1526,1363,1362,1436,1629,1630,1679,241,800,50,1548,93,1761,1634,1635,1633,18,271,5,70,1024,36,1553,828,202,831,281,62,1087,236,944,728,949,1173,1664,212,1617,1293,938,1323,1341,300,224,269,1167,291,1466,1465,1677,1725,1715,1460,1453,210,32,68,98,91,1364,303,1744,1750,1604,15,73,48,25,28,1324,38,34,1597,1089,69,993,821,1288,1181,730,19,24,83,57,71,60,957,850,104,100,1547,92,27,1365,1433,1544,43,59,3,1205,899,67,22,11,94,90,362,209,1376,1379,1411,296,290,822,1661,1207,579,230,286,283,1708,249,252,1574,1702,1595,1623,1668,1378,1389,1335,1672,1498,1462,1337,1507,1331,953,1380,1456,1429,1495,1505,1377,1671,1461,1382,1503,1746,1727,1381,1496,1383,1670,1706,1675,1384,1620,1467,1654,1406,1344,1734,1729,1701,1732,1774,1737,1760)";    // user ids
	$user_sql_result = $mysql->query($user_sql);

	while($row = $user_sql_result->fetch_assoc()){
		$result = '';
	    dates_between($row['id'],$proj_arr[$row['program']],$mysql);
		//echo $insert_sql;
	//	$result = $mysql->query($insert_sql);
	/*	if(!$result){
			echo $row['id']."<br>";
		} else {
			echo "user ID = ".$row['id']." is booked for the project id = ".$proj_arr[$row['program']]."<br><br>";
		}*/
	}

	// function to generate insert quries
	function dates_between($user_id,$project_id,$mysql)
	 {
		 $start_date = '2011-02-14';				//date range to book a user
		 $end_date = '2011-12-31';

		 $insert_string = '';
		 $start_date = is_int($start_date) ? $start_date : strtotime($start_date);
		 $end_date = is_int($end_date) ? $end_date : strtotime($end_date);
		 
		 $end_date += (60 * 60 * 24);

		 $test_date = $start_date;
		 $day_incrementer = 1;
		 
		 do 
		 {
				if(date( "w", $test_date) != 6 && date( "w", $test_date) != 0){	// neglecting sat and sun
					//echo date("Y-m-d", $test_date)."<br>";
					for($i=1;$i<9;$i++){	
						$insert_sql= "INSERT INTO resource_blocks (userid,projectid,daypart,status,datestamp,active,deleted)VALUES(".$user_id.",".$project_id.",".$i.",'3','".date("Y-m-d 00:00:00", $test_date)."','1','0')";
					//	echo $insert_sql;
						$result = $mysql->query($insert_sql);
						if($result != 1){
							echo "record not inserted=".$insert_sql."<br>";
						}
					}
				}
				$test_date = $start_date + ($day_incrementer * 60 * 60 * 24);
		 } while ( $test_date < $end_date && ++$day_incrementer );

	 } 
?>
