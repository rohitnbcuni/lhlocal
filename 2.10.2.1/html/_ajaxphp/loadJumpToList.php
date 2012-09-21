<?PHP 
		include("../_inc/config.inc");
		include("sessionHandler.php");
		$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		$role = $_GET['role'];
		$start_date = $_GET['startDate'];
		$end_date = $_GET['endDate'];
		$company = $_GET['company'];
		$program = $_GET['programType'];
		$fromArrow = $_GET['fromArrow'];	
		$is_filter_changed = $_REQUEST['is_filter_changed'];
  
/*  if($fromArrow == "right" || $fromArrow == "left"){
    if(isset($_COOKIE['lighthouse_rp_data'])){              
      if($fromArrow == 'right'){                    
        $totalDate = calculate_week_start_date($fromArrow);         
        $start_date = date("m-d-Y",$totalDate['startdate']);        
        $end_date = date("m-d-Y",$totalDate['enddate']);        
      }elseif($fromArrow == 'left'){         
        $totalDate = calculate_week_start_date($fromArrow);        
        $start_date = date("m-d-Y",$totalDate['startdate']);         
        $end_date = date("m-d-Y",$totalDate['enddate']);               
      }   
    }
}

  if($start_date != "" && $end_date != ""){
      $start_date = $start_date;
      $end_date = $end_date;
    }else{
      $rpData = explode("%7E",$_COOKIE['lighthouse_rp_data']);
      if($rpData[4] != null && $rpData[4] != '' && $rpData[5] != null && $rpData[5] != ''){     
        $start_date = date("m-d-Y",$rpData[4]);     
        $end_date = date("m-d-Y",$rpData[5]);
       }else{
        $start_date = date("m-d-Y", mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y'),0));
			  $end_date = date("m-d-Y", mktime(1, 0, 0, date('m'), (date('d')-date('w'))+5, date('Y'),0));			  
       }       
    } */


	if($startDate != "" && $endDate != ""){
      $week_start_day = mktime(0,0,0,$start_date_part[0],$start_date_part[1],$start_date_part[2],0);		
      $week_end_day = mktime(0,0,0,$end_date_part[0],$end_date_part[1],$end_date_part[2],0);
    }else{
		if(isset($_COOKIE['lighthouse_rp_data']) && !empty($_COOKIE['lighthouse_rp_data'])){
		  $rpData = explode("%7E",$_COOKIE['lighthouse_rp_data']);
		  if($rpData[4] != null && $rpData[4] != '' && $rpData[5] != null && $rpData[5] != ''){     
			$week_start_day = $rpData[4];     
			$week_end_day = $rpData[5];
		   }else{
			$start_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y'),0)));
		    $end_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, date('m'), (date('d')-date('w'))+5, date('Y'),0)));
		    $week_start_day = mktime(0,0,0,$start_date_part[0],$start_date_part[1],$start_date_part[2],0); 
			$week_end_day = mktime(0,0,0,$end_date_part[0],$end_date_part[1],$end_date_part[2],0);
		   }
		} else {
			$start_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y'),0)));
		    $end_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, date('m'), (date('d')-date('w'))+5, date('Y'),0)));
		    $week_start_day = mktime(0,0,0,$start_date_part[0],$start_date_part[1],$start_date_part[2],0); 
			$week_end_day = mktime(0,0,0,$end_date_part[0],$end_date_part[1],$end_date_part[2],0);
		/*	$week_start_day = strtotime('this monday',time());
			$week_end_day = strtotime('this friday',time()); */
		}
    } 
	
  //process if the request has come through the navigation button  
            
    if($fromArrow == 'right'){                    
		$week_start_day = strtotime('next monday',$week_end_day); 
		$week_end_day = strtotime('next friday',$week_start_day);        
    }elseif($fromArrow == 'left'){         
		$week_start_day = strtotime('previous monday',$week_start_day); 
		$week_end_day = strtotime('next friday',$week_start_day);          
    }

		$html = "";
		if($role == ''){
			$roleSql = '';
		}else{
		    $pos = strpos($role, "parent_");
			$subcategoryFlag = strpos($role, "subcat_");
			$role = str_replace("subcat_","",$role);
			if($subcategoryFlag === false) {
				$flag = 'category';
			}else{
				$flag = 'subcategory';
			}
			if($pos === false){
			      $roleSql = " AND  id IN (SELECT user_id FROM user_roles WHERE category_subcategory_id='$role' AND `flag`='".$flag."')"; 
			}else{ 
				$roleSql = " AND id IN (SELECT user_id FROM user_roles WHERE category_subcategory_id in (SELECT id FROM lnk_user_subtitles WHERE parentTitleId = '".str_replace("parent_","",$role)."') AND `flag`='subcategory')";
			}
		}

	/*	if(($start_date != '') && ($end_date != '')){
			$start_date_part = explode("-", $start_date);
			$end_date_part = explode("-", $end_date);
			//$end_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, $start_date_part[0], $start_date_part[1]+4, $start_date_part[2])));
		}else{
			$start_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y'),0)));
			$end_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, date('m'), (date('d')-date('w'))+5, date('Y'),0)));
		}

		$start_day = mktime(0,0,0,$start_date_part[0],$start_date_part[1],$start_date_part[2],0);
		$end_day = mktime(0,0,0,$end_date_part[0],$end_date_part[1],$end_date_part[2],0); */

		if($company != ""){
			$companyFilter = ' AND id IN (SELECT distinct rb.userid from resource_blocks rb, projects pj WHERE pj.company="' . $company . '" AND rb.`datestamp` >= "' . date("Y-m-d", $week_start_day) . ' 00:00:00" AND rb.`datestamp` <= "' . date("Y-m-d", $week_end_day) . ' 00:00:00" AND ((rb.`daypart` <> "9" AND rb.status  is not null) OR (rb.`daypart` = "9" AND rb.hours >0)) AND rb.projectid=pj.id)';
		}else{
			$companyFilter = '';
		}
		if($program !=""){
			$programFilter = ' program = "'.$program.'" AND';
		} else {
			$programFilter = '';
		}

		$sql_user = "SELECT last_name FROM `users` WHERE `company`='2' AND $programFilter `deleted`='0' $roleSql $companyFilter ORDER BY `last_name`";
		$user_res = $mysql->query($sql_user);
		$firstChar = '';
		$firstCharFlag = true;
		if($user_res->num_rows > 0) {
			$alpha = "";
			$html .= '<li class="jumpto">Jump To</li>';
			while($user_row = $user_res->fetch_assoc()) {
				$char = strtolower(substr($user_row['last_name'], 0, 1));
				if($firstCharFlag){
					if($is_filter_changed == 1){
						$firstChar = 'All';
					} else {
						$firstChar = $char;
					}
				}
				if($alpha != ucfirst(substr($user_row['last_name'], 0, 1))) {
					$html .= '<li class="jumptoItem"><span>' . $char .'</span></li>';
				}
				$alpha = ucfirst(substr($user_row['last_name'], 0, 1));
				$firstCharFlag = false;
			}
			$html .= '<li class="jumptoItem"><span>All</span></li>';
		}


  /*  function calculateDate($weekdayValue,$weekStartEndDayValue){    
  $weekday = date("D",$weekdayValue);           
    if($weekStartEndDayValue == "startDate"){      
      if($weekday == "Mon"){        
        return $weekdayValue;      
      }elseif($weekday == "Sat" || $weekday == "Sun"){        
        return strtotime('next monday',$weekdayValue);     
      }else{        
        return strtotime('previous monday',$weekdayValue);      
      }    
    }            
  
    if($weekStartEndDayValue == "endDate"){      
      if($weekday == "Fri"){        
        return $weekdayValue;      
      }elseif($weekday == "Sat" || $weekday == "Sun"){        
        return strtotime('previous friday',$weekdayValue);      
      }else{        
        return strtotime('next friday',$weekdayValue);      
      }    
    }
  }     */       
  
 /* function calculate_week_start_date($fromArrow){          
    $rpData = explode("%7E",$_COOKIE['lighthouse_rp_data']);     
    $week_start_day = $rpData[4];     
    $week_end_day = $rpData[5];                         
    $dateDiff = $week_end_day - $week_start_day;      
    $dateDiff = $dateDiff + 86400;            
      if($fromArrow == 'right'){        
        $week_start_day += $dateDiff;       
        $week_end_day += $dateDiff;    
      }elseif($fromArrow == 'left'){     
        $week_start_day -= $dateDiff;    
        $week_end_day -= $dateDiff;     
      }                      
    $week_start_day = calculateDate($week_start_day,'startDate');               
    $week_end_day = calculateDate($week_end_day,'endDate');      
    $totalDate['startdate'] = $week_start_day;      
    $totalDate['enddate'] = $week_end_day;      
    return $totalDate;         
  }	*/
  
  
  //function to calculate `Monday` of the Start Date and `Friday` of End Date 
  function calculateDate($weekdayValue,$weekStartEndDayValue){    
  $weekday = date("D",$weekdayValue);           
    if($weekStartEndDayValue == "startDate"){      
      if($weekday == "Mon"){        
        return $weekdayValue;      
      }else{        
        return strtotime('previous monday',$weekdayValue);      
      }    
    }            
  
    if($weekStartEndDayValue == "endDate"){      
      if($weekday == "Fri"){        
        return $weekdayValue;      
      }else{        
        return strtotime('next friday',$weekdayValue);      
      }    
    }
  } 
  
  //function to calculate NEXT & PREVIOUS days on click of Navigation button
   function calculate_week_start_date($fromArrow){          
    $rpData = explode("%7E",$_COOKIE['lighthouse_rp_data']);     
    $week_start_day = $rpData[4];     
    $week_end_day = $rpData[5];                         
    $dateDiff = $week_end_day - $week_start_day;  
    $fullDays = floor($dateDiff/(60*60*24)); 
    $fullDays += 1;                
      if($fromArrow == 'right'){     
        $week_start_day += 86400 * $fullDays;
        $week_end_day += 86400 * $fullDays;   
      }elseif($fromArrow == 'left'){             
        $week_start_day -= 86400 * $fullDays;
        $week_end_day -= 86400 * $fullDays;         
      }                            
    $week_start_day = calculateDate($week_start_day,'startDate');               
    $week_end_day = calculateDate($week_end_day,'endDate');
    
    $week_start_day = mktime(0,0,0,date('m',$week_start_day),date('d',$week_start_day),date('Y',$week_start_day),0);
    $week_end_day = mktime(0,0,0,date('m',$week_end_day),date('d',$week_end_day),date('Y',$week_end_day),0);
            
    $totalDate['startdate'] = $week_start_day;      
    $totalDate['enddate'] = $week_end_day;      
    return $totalDate;         
  }
  
  
  
  


		echo $html . '--' . $firstChar;
?>
