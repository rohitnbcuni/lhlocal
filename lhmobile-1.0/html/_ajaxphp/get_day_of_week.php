<?PHP
	if($_GET['status'] == 'new'){
		$date = $_GET['date'];
		$hour = $_GET['hour']; // the time is not taken into picture to calculate the end date. This can be used for the enhancement.
		$type = $_GET['type'];

		$date_part = explode("/", $date);
		$skip_day = date("D", mktime(0, 0, 0, $date_part[0], $date_part[1], $date_part[2]));

		$add_Date = 0;
		switch($type){
			case '1':
					// SLA => 24 hrs
					$add_Date = 3;
					if($skip_day == 'Fri'){
						$add_Date += 2;
					}else if($skip_day == 'Sat'){
						$add_Date += 1;
					}else if ($skip_day == 'Sun'){
						$add_Date += 0;
					}/*else if($hour == 8){
						$add_Date = 2;
						if($skip_day == 'Thu' || $skip_day == 'Fri'){
							$add_Date += 2;
						}
					}else{
						if($skip_day == 'Thu' || $skip_day == 'Fri'){
							$add_Date += 2;
						}
					}*/
				break;
			case '2':
					// SLA => 12 hrs
					$add_Date = 1;
					if($skip_day == 'Sat'){
						$add_Date += 2;
					}else if ($skip_day == 'Sun'){
						$add_Date += 1;
					}/*else if($hour >= 15){
						$add_Date = 2;
					}else{
						$add_Date = 1;
					}*/
				break;
			case '3':
					// SLA => 4 hrs
					if($skip_day == 'Sat'){
						$add_Date = 2;
					}else if ($skip_day == 'Sun'){
						$add_Date = 1;
					}/*else if($hour >= 15){
						$add_Date = 1;
					}else{
						$add_Date = 0;
					}*/
				break;
			case '4':
					// SLA => 2 hrs
					if($skip_day == 'Sat'){
						$add_Date = 2;
					}else if ($skip_day == 'Sun'){
						$add_Date = 1;
					}/*else if($hour <= 16){
						$add_Date = 0;
					}else{
						$add_Date = 1;
					}*/
				break;
		}

		$day = date("D", mktime(0, 0, 0, $date_part[0], $date_part[1]+$add_Date, $date_part[2]));

		if($day == "Sat") {
			$add_Day = 2;
		} else if($day == "Sun") {
			$add_Day = 2;
		}else {
			$add_Day = 0;
		}

		echo  date("m/d/Y", mktime(0, 0, 0, $date_part[0], ($date_part[1]+ $add_Date + $add_Day), $date_part[2]));

	}else{
		if(isset($_GET['date'])) {
			/*    month/day/year      */
			$date_part = explode("/", $_GET['date']);
			$add = $date_part[1];
			
			$day = date("D", mktime(0, 0, 0, $date_part[0], $date_part[1], $date_part[2]));
			
			if($day == "Sat" || $day == "Sun") {
				if($day == "Sat") {
					$add = $date_part[1]+2;
				} else {
					$add = $date_part[1]+1;
				}
			}
			
			echo $add;
		}
	}
?>