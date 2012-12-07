<?PHP 		
  session_start();		
  include("../_inc/config.inc");
  include("sessionHandler.php");		
  //$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);	
  global $mysql;
  $html = "";		
  $character = trim(urldecode($mysql->real_escape_string($_POST['showUser'])));		
  $startDate = $mysql->real_escape_string($_POST['startDate']);		
  $endDate = $mysql->real_escape_string($_POST['endDate']);		
  $fromArrow = $mysql->real_escape_string($_POST['fromArrow']);	    
  $basicValue = $mysql->real_escape_string($_POST['basicValue']);					
  $role = $mysql->real_escape_string($_POST['role']);	
  $selectedRole = $role;	
  $savedCompany = $mysql->real_escape_string($_POST['company']);		
  $loopCount = 0;
  $start_date_part = explode("-", $startDate);		
  $end_date_part = explode("-", $endDate);
  $savedProgram = $mysql->real_escape_string($_REQUEST['programType']);	
  

/*if(isset($savedProgram) || isset($role) ||isset($savedCompany)) {
	$character ='all';
}*/
  if(isset($_REQUEST['filterChanged']) && $_REQUEST['filterChanged'] == '1' ){
	if($fromArrow != 'right' && $fromArrow != 'left' ){
		$character ='all';
	}else if(!$fromArrow){
		$character = $character;
	}
  } 

  if('all' == strtolower($character)){		
    $charLimit = "";		
  }elseif($character == "" || $character == null || $character == "undefined"){	
    $charLimit = " AND `last_name` like 'a%' ";  	
  }else{
    $charLimit = " AND `last_name` like '$character%' ";	
  }

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


 //total width of the right content 		
  $dateDiff = $week_end_day - $week_start_day;		
  $schedule_width = floor($dateDiff/(60*60*24));    				
  $schedule_width = ($schedule_width+1) * 101;            
  $start_day_loop = $week_start_day;    
  $end_day_loop = $week_end_day;           
  /* loop to remove SATURDAYS & SUNDAYS from the list &      
  calculate required width of the right content     
  */       
  while($start_day_loop<=$end_day_loop) {        	      
  if(date("D",$start_day_loop) == "Sat" || date("D",$start_day_loop) == "Sun"){       
    $schedule_width = $schedule_width - 101;          	        
  }        
    $start_day_loop += 86400; //add 24 hours   
  }	
  
  if($savedCompany != ""){  
    $companyFilter = ' AND id IN (SELECT distinct rb.userid from resource_blocks rb, projects pj WHERE pj.company="' . $savedCompany . '" AND rb.`datestamp` >= "' . date("Y-m-d", $week_start_day) . ' 00:00:00" AND rb.`datestamp` <= "' . date("Y-m-d", $week_end_day) . ' 00:00:00" AND ((rb.`daypart` <> "9" AND rb.status is not null) OR (rb.`daypart` = "9" AND rb.hours >0)) AND rb.projectid=pj.id)';		
  }else{		
    $companyFilter = '';	
  }		
  if($savedProgram != ""){
	$program_filter = " program = '".$savedProgram."' AND";
  } else {
	$program_filter = "";
  }

  if($role == ''){			
    $sql_user = "SELECT * FROM `users` WHERE `company`='2' AND $program_filter `deleted`='0' $charLimit $companyFilter ORDER BY `last_name`";		
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
				$sql_user = "SELECT * FROM `users` WHERE `company`='2' AND $program_filter `deleted`='0' AND id IN (SELECT user_id FROM user_roles WHERE category_subcategory_id='$role' AND `flag`='".$flag."') $charLimit  $companyFilter ORDER BY `last_name`";
			}else{
				$sql_user = "SELECT * FROM `users` WHERE `company`='2' AND $program_filter `deleted`='0' AND id IN (SELECT user_id FROM user_roles WHERE category_subcategory_id in (SELECT id FROM `lnk_user_subtitles` WHERE parentTitleId = '".str_replace("parent_","",$role)."') AND `flag`='subcategory') $charLimit  $companyFilter ORDER BY `last_name`";
		}
  }    
  $schedule_width_header = $schedule_width + 10;	
  $user_res = $mysql->sqlordie($sql_user);   
  $htmlHeader = '<div class="resources_controller" style="width:'.$schedule_width_header.'px;padding-left:119px;min-width:515px;"><ul class="days_container" style="width:100%;"><li class="arrows"><button class="arrows arrows_left"></button></li>';		
  if($user_res->num_rows > 0) {			
  $alpha = "";			
  $loop = 0;			
  while($user_row = $user_res->fetch_assoc()) {			
    $start_day = $week_start_day;				
    $end_day = $week_end_day;			
    $sel_class_flag = true;				
    $returnString = resourceBlockLockHtml(date("n-j-Y", $start_day), date("W", $start_day), $user_row['id'], '0', $mysql);				
  if(stristr($returnString, 'rp_edit') === FALSE && $_SESSION['login_status'] != 'admin'){					
    $sel_class_flag = false;			
  }				
  $sql_title = "SELECT * FROM `lnk_user_titles` WHERE `id`= ?  LIMIT 1";
  $title_res = $mysql->sqlprepare($sql_title, array($user_row['user_title']));		
  $title_row = $title_res->fetch_assoc();			
  //if($alpha != ucfirst(substr($user_row['last_name'], 0, 1))) {					
  $id = ucfirst(substr($user_row['last_name'], 0, 1));				
  //}				
  $sqlrpOt = "SELECT COUNT(a.`id`) as total FROM `resource_blocks` a WHERE a.`userid`='" .$user_row['id'] ."' AND a.`datestamp` = '" .date("Y-m-d", $start_day) ." 00:00:00' AND a.`daypart` = '9' AND `hours` > 0 ORDER BY a.`datestamp`";				
  $resrpOt = @$mysql->sqlordie($sqlrpOt);			
  if($resrpOt->num_rows > 0) {					
    $resrpOt_row = $resrpOt->fetch_assoc();					
  if(@$resrpOt_row['total'] > 0) {						
    $otClass = "cancel";					
  } else {						
    $otClass = "overtime";					
  }				
  } 
  else {					
  $otClass = "overtime";				
  }    
      				
  //$overtimeID = 'overtime_' .$user_row['id'];				
  $html .= '<div class="schedules_row title_' .$title_row['id'] .' sort_' .$id .'" style="min-width:628px;width:auto;">						
  <div class="schedule_owner" style="min-width:628px;width:auto;padding:10px 0 0 5px;">						
  <strong style="width:100px;min-height:45px;">' .ucfirst($user_row['last_name']) .',<br>' .ucfirst($user_row['first_name']) .'</strong>							
  					
  <button class="secondary viewmonth" id="viewmonth_' .$user_row['id'] .'"><span>view month</span></button><br><br><br>							
  <!-- <button onclick="displayOvertime(\'' . $overtimeID . '\')" id="' . $overtimeID . '" class="' .$otClass .'"><span>+overtime</span></button> -->					
  </div>            
  <ul class="schedule" style="float:left;overflow:visible;width:'.$schedule_width.'px;margin-top:-95px;margin-left:110px;padding-top:5px;">';
  $next_day = "";						
  $day = 1;						
  $hour = 1;						
  $col = 1;            						
  while($start_day<=$end_day) {	
    $loopCount++;											
    $isfirst=""; 																		
    
  //if the day is a weekend, skip this particular loop excecution						
  if(date("D",$start_day) == "Sat" || date("D",$start_day) == "Sun"){							
    $start_day += 86400; //add 24 hours   
    $loopCount--;            
    continue;            
  }            					            
  if($col==1){                
    $isfirst='first_day ';            
  }                                 
  
  if($start_day == $end_day){               
  $isfirst='last_day ';           
  }                     
  if($loop == 0){           
  $dateDiff2 = $end_day - $start_day;  		      
  $dateDiff2 = floor($dateDiff2/(60*60*24));  		        		     
  
  if(($dateDiff2 == 2 || $dateDiff2 == 1) && $loop==0){                         
    if(date('D', strtotime('+1 day', $start_day))== "Sat"){  
      $isfirst='last_day ';              
    }           
  }          
  }
                              						
  if($loop == 0){											
  
  // creating header section separately & appending it later            
  $htmlHeader = $htmlHeader.'<li class="'.$isfirst.$col.'_col">            
  <span>'.date("D",$start_day).'</span><br><span>'.date("m",$start_day).'/'.date("j",$start_day).'</span>           
  <span class="days_full_date">'.date("n",$start_day)."/".date("j",$start_day)."/".date("Y",$start_day).'</span></li>';            
  }            					
  
  $html .= '<li class="schedule_day">							            								
  <ul>';
  //$sqlrp = "SELECT COUNT(a.`id`) FROM `resource_blocks` a LEFT JOIN `projects` b ON a.`projectid`=b.`id`  WHERE a.`userid`='" .$user_row['id'] ."' AND a.`datestamp` = '" .date("Y-m-d", $start_day) ." 00:00:00' ORDER BY a.`datestamp`, a.`daypart`";
  //$resrp = $mysql->query($sqlrp);								
  for($i = 0; $i < 8; $i++) {								
  $sqlrp = "SELECT * FROM `resource_blocks` a LEFT JOIN `projects` b ON a.`projectid`=b.`id`  WHERE a.`userid`= ? AND a.`datestamp` = ? AND a.`daypart` = ? ORDER BY a.`datestamp`, a.projectid desc Limit 0,1 ";								
  $resrp = $mysql->sqlprepare($sqlrp,array($user_row['id'],date("Y-m-d", $start_day) .' 00:00:00',  ($i+1)) );							
  if($resrp->num_rows == 1) {						
    $rpRow = $resrp->fetch_assoc();			
    $status = "";									
    switch($rpRow['status']) {											
    case 1: {												
    $status = "overhead";												
    break;											
    }											
    case 2: {											
    $status = "outofoffice";												
    break;											
    }											
    case 3: {												
    $status = "allocated";												
    break;											
    }											
    case 4: {												
    $status = "convert";												
    break;											
    }											
    default: {											
    $status = "";											
    break;											
    }									
    }										
  
  if($savedCompany != ""  && $savedCompany != $rpRow['company']){											
  $cell_selectable = "unavailable ";										
  }else{											
  if($sel_class_flag)												
  $cell_selectable = "sel ";											
  else												
  $cell_selectable = "";										
  }																				
  
  if(empty($rpRow['projectid'])) {											
  $html .=  '<li id="dayblock_' .$user_row['id'] .'_' .$col ."_" .($i+1) .'" class="' . $cell_selectable . $status . '"><div class="slot_label"></div><div class="slot_title"></div></li>';										
  } else {											
    if(empty($rpRow['project_name'])) {												
      $name = "";												
      $tootltip = "";											
    } else {												
      $name = $rpRow['project_code'] .": " .$rpRow['project_name'];												
      $full_name = $name;												
      if(strlen($name) > 20) {													
        $tootltip = $name;													
        $name = substr($name, 0, 20) ."...";											
      } else {													
        $tootltip = "";												
      }											
    }											
    
    $html .=  '<li id="dayblock_' .$user_row['id'] .'_' .$col ."_" .($i+1) .'" class="' . $cell_selectable . $status . '"><div class="slot_label" title="' . $full_name . '">' .$name .'</div><div class="slot_title">' .$tootltip .'</div></li>';										
  }									
  } else {										
  $html .=  '<li id="dayblock_' .$user_row['id'] .'_' .$col ."_" .($i+1) .'" class="sel"><div class="slot_label"></div><div class="slot_title"></div></li>';									
  }								
  }								
  
  $html .=  '</ul>							
  </li>';							
  $start_day += 86400; //add 24 hours							
  $col++;						
  }						
  $html .=  '</ul>					
  </div>';                
  $loop++;				
  $alpha = ucfirst(substr($user_row['last_name'], 0, 1));			
  }	       
  
  //appending header section to the content			 
  $htmlHeader = $htmlHeader.'<li class="arrows"><button class="arrows arrows_right"></button></li></ul></div>';			        
  
  //display date range on the header       
  $html = $htmlHeader.$html.'<script type="text/javascript">              
  var sd = new Date('.$week_start_day.' * 1000);      
  var sdYear = sd.getFullYear();       sdYear = sdYear.toString().slice(2);              
  var ed = new Date('.$week_end_day.' * 1000);       
  var edYear = ed.getFullYear();       
  edYear = edYear.toString().slice(2);                  
  $(".week_label").html("for "+(sd.getMonth()+1)+"/"+sd.getDate()+"/"+sdYear + " - " + (ed.getMonth()+1)+"/"+ed.getDate()+"/"+edYear);';	
  if($loopCount == 0 || $loopCount == "" ){
    $html .= '$(".arrows").css("display","none");</script>'; 
  }else{
    $html .= '</script>';
  }		
  }
  
  function resourceBlockLockHtml($date, $week, $user, $first_week_flag, $mysql){				  
    $date_array = explode("-", $date);			
    $resourceBlockSql = 'SELECT 1 from `resource_planner_lock` WHERE `user_id`="' .$user. '" AND `week_num`="' .$week. '" AND `year`="' .$date_array[2]. '" AND `active`="1"';			
    $result = $mysql->sqlordie($resourceBlockSql);			
    if($result->num_rows == 0){				
      $offset = array("1" => '0', "2" => '1', "3" => '2', "4" => '3', "5" => '4');
      if($first_week_flag == '1'){
        $start_day = date('w', mktime(0, 0, 0, $date_array[0], $date_array[1], $date_array[2],0));
        $start_date = date('Y-n-j', mktime(0, 0, 0, $date_array[0], $date_array[1]-$offset[$start_day], $date_array[2],0));
      }else{
        $start_date = date('Y-n-j', mktime(0, 0, 0, $date_array[0], $date_array[1], $date_array[2],0));
      }				
      $html = '<button class="rp_edit" id="lock_week_'.$week.'" onclick="completeWeek(\'' .$start_date. '\', \'' .$week. '\', \'' .$user. '\')"><span>Submit</span></button>';			
    }else{				
      $html = '<button class="rp_complete" ><span>Submit</span></button>';		
    }					
    return $html;	
  }			
  
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
  
  
  
  setcookie("lighthouse_rp_data", urlencode($selectedRole . '~' . $startDate . '~' . $character . '~' . $savedCompany . '~' . $week_start_day . '~' . $week_end_day . '~' . $savedProgram), time()+220752000, '/');    
  echo $html;
  ?>
