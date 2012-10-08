<?PHP
/*
	Control Tower classes parent and child
*/
class ResourcePlanner {

	protected static $create_new;
	protected static $edit;
	protected static $configuration;

	public function setIniFile() {
		$this->configuration = new Zend_Config_Ini(
		    APPPATH . '/config/app.ini',
		    APPLICATION_ENVIRONMENT
		);
	}

	public function getIniFile() {
		return $this->configuration;
	}

}

class RpDisplay extends ResourcePlanner {

	public function getQuery($query) {
		/*ResourcePlanner::setIniFile();

		$config = ResourcePlanner::getIniFile();

		$db = Zend_Db::factory('Pdo_Mysql', array(
				'host'     => $config->database->params->host,
			    'username' => $config->database->params->username,
			    'password' => $config->database->params->password,
			    'dbname'   => $config->database->params->dbname,
			    'port'   => $config->database->params->dbport
			));
		$db->getConnection();*/
		//get DB connection from registry LH#20736
        $db = Zend_Registry::get('db'); 
		$data = $db->fetchAll($query);

		return $data;
	}

	public function projectList() {
		$projects = self::getQuery("SELECT * FROM `projects` WHERE `archived`='0' AND `active`='1' AND `deleted`='0'");

		return $projects;
	}

	public function getCompanyBcHTML() {

	}

	public function getUserInfo($userID) {
		$select_resources = "SELECT * FROM `users` WHERE id='$userID' AND `deleted`='0'";
		$resc = self::getQuery($select_resources);
		return $resc[0]['first_name']." ".$resc[0]['last_name'];

	}

	public function resourceListHTML($savedRole='') {
		$select_titles = "SELECT * FROM `lnk_user_titles` where active='1' ORDER BY `sort` ASC";
		$usersTitleSubCategoryList = self::getQuery("SELECT * FROM `lnk_user_subtitles` WHERE `active`='1' AND `deleted`='0' ");
		$resc = self::getQuery($select_titles);
		for($i=0;$i<count($usersTitleSubCategoryList);$i++){
			$detailsArray['name'] = $usersTitleSubCategoryList[$i]['name'];
			$detailsArray['id'] = $usersTitleSubCategoryList[$i]['id'];
			$subCategoryArray[$usersTitleSubCategoryList[$i]['parentTitleId']][]=$detailsArray; 
		} 
		$html = "";
   
		for($p = 0; $p < sizeof($resc); $p++) {
			$selected = '';
			if($savedRole == $resc[$p]['id']){
				$selected = ' SELECTED ';
			}
			if(count($subCategoryArray[$resc[$p]['id']])<=0){ 
				$html .= "<option value=\"". $resc[$p]['id'] ."\" " . $selected . ">". $resc[$p]['name'] ."</option>";
			}else{
				$html .= '<optgroup label="'.$resc[$p]['name'].'">'; 
				$allSelected = "";
				if("parent_".$resc[$p]['id'] == $savedRole){
					$allSelected = " SELECTED";
				}
				$html .= '<option value="parent_' .$resc[$p]['id'] .'" '.$allSelected.'>ALL</option>';
				$categoryDetails = $subCategoryArray[$resc[$p]['id']];  
				for($j = 0; $j < count($categoryDetails ); $j++) {
					if("subcat_".$categoryDetails[$j]['id']==$savedRole) {
						$selected = " SELECTED";
					}else{
						$selected = "";
					} 
					$html .= '<option value="subcat_' .$categoryDetails [$j]['id'] .'"' .$selected .'>' .$categoryDetails [$j]['name'] .'</option>';
				}
				$html .= "</optgroup>";
			}
		}
		return $html;
	}

	public function fullProjectListHTML() {
		$html = '';

			$select_projects = "SELECT * FROM `projects` WHERE `archived` = '0' AND `active` = '1' AND `deleted` = '0' ORDER BY `project_code`, `project_name` ASC";
			$projs = self::getQuery($select_projects);

			if (sizeof($projs)>0) {
				for($p = 0; $p < sizeof($projs); $p++) {
					$html .= '<option id="proj' . $projs[$p]['id'] . '" value="' . $projs[$p]['id'] . '">' . $projs[$p]['project_code'] . ' - ' . $projs[$p]['project_name'] . '</option>';
				}
			}

		return $html;
	}

	public function fullCompanyListHTML($companyid) {
		$html = '';

			$comps = self::getQuery(QRY_COMPANIES_ASC);
			$numOfComps = sizeof($comps);
			if ($numOfComps > 0) {
				for($p = 0; $p < $numOfComps; $p++) {
					if($comps[$p]['id'] == $companyid){
						$selected = ' SELECTED ';
					}else{
						$selected = '';
					}
					$html .= '<option id="company_' . $comps[$p]['id'] . '" value="' . $comps[$p]['id'] . '" ' . $selected . ' title=" '. $comps[$p]['name'] .'">' . $comps[$p]['name'] . '</option>';
				}
			}

		return $html;
	}

	public function projectListHTML() {
		$html = '';
		$comp = self::getQuery(QRY_COMPANIES_ASC);

		$html = '';
		$class = 'even';
		for($i = 0; $i < sizeof($comp); $i++) {
			//$html .= '<option value="' .$comp[$i]['id'] .'_' .$comp[$i]['bc_id'] .'">' .$comp[$i]['name'] .'</option>';


			$compID = $comp[$i]['id'];
			$select_projects = "SELECT * FROM `projects` WHERE `company` = '$compID' AND`archived` = '0' AND `active` = '1' AND `deleted` = '0' AND `rp_permission` = '1' ORDER BY `project_code`,`project_name` ASC";
			$projs = self::getQuery($select_projects);

			if (sizeof($projs)>0) {
				if ($class=='even') {
					$class='odd';
				} else {
					$class='even';
				}
				$company_name = (strlen($comp[$i]['name']) > 34)?substr($comp[$i]['name'],0,32)."..":$comp[$i]['name'];	
				
				$html .= '<li class="'.$class.'"><div class="inside rp_rightarrow" title="'.$comp[$i]['name'].'"><div class="arrow_right"  style="padding-top: 2px;"></div>' .$company_name .'</div>
							<ul id="drop'.$i.'">';
				for($p = 0; $p < sizeof($projs); $p++) {
					if(strlen($projs[$p]['project_name']) > 45) {
						$elipse = "...";
					} else {
						$elipse = "";
					}
					//$html .= '<li id="projid_' . $projs[$p]['id'] . '" title="' . $projs[$p]['project_code'] . ': ' . $projs[$p]['project_name'] . '">' . substr($projs[$p]['project_name'], 0, 45) . '</li>';
					$html .= '<li id="projid_' . $projs[$p]['id'] . '" name="' .$projs[$p]['project_code'] . ': ' . $projs[$p]['project_name'] .'">' .$projs[$p]['project_code'] . ' - ' . substr($projs[$p]['project_name'], 0, 45) . '</li>';
				}
				$html .= '	</ul>
					</li>';
				}
		}


		return $html;
	}

	public function fullListInit($savedProgram='' , $savedRole='', $savedStartDate='', $is_filter_selected = '', $savedCompany='',$savedEndDate='') {
		ResourcePlanner::setIniFile();
		$html = "";
		$loopCount = 0;

		$config = ResourcePlanner::getIniFile();

		$mysql = new mysqli($config->database->params->host, $config->database->params->username, $config->database->params->password, $config->database->params->dbname,$config->database->params->dbport);

		if($savedStartDate != '' && $savedEndDate != ''){
			$start_date_part = explode("-", $savedStartDate);
			//$end_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, $start_date_part[0], $start_date_part[1]+4, $start_date_part[2])));
		  $end_date_part = explode("-", $savedEndDate);
    }else{
			$start_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y'),0)));
			$end_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, date('m'), (date('d')-date('w'))+5, date('Y'),0)));
		}

		$week_start_day = mktime(0,0,0,$start_date_part[0],$start_date_part[1],$start_date_part[2],0);
		$week_end_day = mktime(0,0,0,$end_date_part[0],$end_date_part[1],$end_date_part[2],0);



    $dateDiff = $week_end_day - $week_start_day;
		$schedule_width = floor($dateDiff/(60*60*24));
		
          
    //total width of the right content      		
		$schedule_width = ($schedule_width+1) * 101; 
    $start_day_loop = $week_start_day;
    $end_day_loop = $week_end_day;
     while($start_day_loop<=$end_day_loop) {        	
      if(date("D",$start_day_loop) == "Sat" || date("D",$start_day_loop) == "Sun"){
        $schedule_width = $schedule_width - 101;          	   
      }
        $start_day_loop += 86400; //add 24 hours
    }
    $schedule_width_header = $schedule_width + 10;

		if($is_filter_selected == "" || $is_filter_selected == "undefined" || $is_filter_selected == null ){
			$charSql = " AND last_name like 'a%' ";
		}else if($is_filter_selected == "1"){
			$charSql = "";
		}
		if($savedProgram!= ""){
			$programFilter = ' program = "'.$savedProgram.'" AND ';
		} else {
			$programFilter = '';
		}

		if($savedCompany != ""){
			$companyFilter = ' AND id IN (SELECT distinct rb.userid from resource_blocks rb, projects pj WHERE pj.company="' . $savedCompany . '" AND rb.`datestamp` >= "' . date("Y-m-d", $week_start_day) . ' 00:00:00" AND rb.`datestamp` <= "' . date("Y-m-d", $week_end_day) . ' 00:00:00" AND ((rb.`daypart` <> "9" AND rb.status is not null) OR (rb.`daypart` = "9" AND rb.hours >0)) AND rb.projectid=pj.id)';
		}else{
			$companyFilter = '';
		}
		
		if($savedRole != ''){
			$pos = strpos($savedRole, "parent_");
			$subcategoryFlag = strpos($savedRole, "subcat_");
			$savedRole = str_replace("subcat_","",$savedRole);
			if($subcategoryFlag === false) {
				$flag = 'category';
			}else{
				$flag = 'subcategory';
			}
			if($pos === false){
				$roleSql = " AND  id IN (SELECT user_id FROM user_roles WHERE category_subcategory_id='$savedRole' AND `flag`='".$flag."')";  
				$sql_user = "SELECT * FROM `users` WHERE `company`='2' AND $programFilter $programFilter `deleted`='0' " . $roleSql . " $charSql $companyFilter ORDER BY `last_name`";
			}else{
				$roleSql = " AND id IN (SELECT user_id FROM user_roles WHERE category_subcategory_id in (SELECT id FROM lnk_user_subtitles WHERE parentTitleId = '".str_replace("parent_","",$savedRole)."') AND `flag`='subcategory')";
				$sql_user = "SELECT * FROM `users` WHERE `company`='2' AND $programFilter `deleted`='0' " . $roleSql . " $charSql $companyFilter ORDER BY `last_name`";
			}
		}else{
			$sql_user = "SELECT * FROM `users` WHERE `company`='2' AND $programFilter `deleted`='0'  $charSql $companyFilter ORDER BY `last_name`";
		}
	//	echo "qry=".$sql_user;die;
		$user_res = $mysql->query($sql_user);
		$htmlHeader = '<div class="resources_controller" style="width:'.$schedule_width_header.'px;padding-left:119px;min-width:515px;"><ul class="days_container" style="width:100%;"><li class="arrows"><button class="arrows arrows_left"></button></li>';

		if($user_res->num_rows > 0) {
			$alpha = "";
			$loop = 0;
			while($user_row = $user_res->fetch_assoc()) {

				$start_day = $week_start_day;
				$end_day = $week_end_day;

				$sel_class_flag = true;
				$returnString = self::resourceBlockLockHtml(date("n-j-Y", $start_day), date("W", $start_day), $user_row['id'], '0');
				if(stristr($returnString, 'rp_edit') === FALSE && $_SESSION['login_status'] != 'admin'){
					$sel_class_flag = false;
				}
				$sql_title = "SELECT * FROM `lnk_user_titles` WHERE `id`='" .$user_row['user_title'] ."' LIMIT 1";
				$title_res = $mysql->query($sql_title);
				$title_row = $title_res->fetch_assoc();
				//if($alpha != ucfirst(substr($user_row['last_name'], 0, 1))) {
					$id = ucfirst(substr($user_row['last_name'], 0, 1));
				//}
				$sqlrpOt = "SELECT COUNT(a.`id`) as total FROM `resource_blocks` a WHERE a.`userid`='" .$user_row['id'] ."' AND a.`datestamp` = '" .date("Y-m-d", $start_day) ." 00:00:00' AND a.`daypart` = '9' AND `hours` > 0 ORDER BY a.`datestamp`";
				$resrpOt = @$mysql->query($sqlrpOt);

				if($resrpOt->num_rows > 0) {
					$resrpOt_row = $resrpOt->fetch_assoc();
					if(@$resrpOt_row['total'] > 0) {
						$otClass = "cancel";
					} else {
						$otClass = "overtime";
					}
				} else {
					$otClass = "overtime";
				}

				$overtimeID = 'overtime_' .$user_row['id'];
//TO remove the develooper title
//							<em>' .$title_row['name'] .'</em>
				$html .= '<div class="schedules_row title_' .$title_row['id'] .' sort_' .$id .'" style="min-width:628px;width:auto;">
						<div class="schedule_owner" style="width:100%;_width:628px;*width:628px;padding-left:3px;">
							<strong style="width:100px;min-height:45px;">' .ucfirst($user_row['last_name']) .',<br>' .ucfirst($user_row['first_name']) .'</strong>
							<button class="secondary viewmonth" id="viewmonth_' .$user_row['id'] .'"><span>view month</span></button>
							<!-- <br><br><br><button onclick="displayOvertime(\'' . $overtimeID . '\')" id="' . $overtimeID . '" class="' .$otClass .'"><span>+overtime</span></button> -->
						</div>
						<ul class="schedule" style="width:'.$schedule_width.'px;margin-left:111px;margin-top:-80px;float:left;">';

						$next_day = "";
						$day = 1;
						$hour = 1;


						$col = 1;
						while($start_day<=$end_day) {
						$loopCount++;	
					  if(date("D",$start_day) == "Sat" || date("D",$start_day) == "Sun"){
							$start_day += 86400; //add 24 hours
							$loopCount--;
              continue;
            }
						
					  $isfirst=""; 
						
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

//							$sqlrp = "SELECT COUNT(a.`id`) FROM `resource_blocks` a LEFT JOIN `projects` b ON a.`projectid`=b.`id`  WHERE a.`userid`='" .$user_row['id'] ."' AND a.`datestamp` = '" .date("Y-m-d", $start_day) ." 00:00:00' ORDER BY a.`datestamp`, a.`daypart`";
//							$resrp = $mysql->query($sqlrp);

								for($i = 0; $i < 8; $i++) {
									$sqlrp = "SELECT * FROM `resource_blocks` a LEFT JOIN `projects` b ON a.`projectid`=b.`id`  WHERE a.`userid`='" .$user_row['id'] ."' AND a.`datestamp` = '" .date("Y-m-d", $start_day) ." 00:00:00' AND a.`daypart` = '" .($i+1) ."' ORDER BY a.`datestamp`";
									$resrp = $mysql->query($sqlrp);

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
											$html .=  '<li id="dayblock_' .$user_row['id'] .'_' .$col ."_" .($i+1) .'" class="' . $cell_selectable .$status .'"><div class="slot_label"></div><div class="slot_title"></div></li>';
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
											$html .=  '<li id="dayblock_' .$user_row['id'] .'_' .$col ."_" .($i+1) .'" class="' . $cell_selectable .$status .'"><div class="slot_label" title="' . $full_name . '">' .$name .'</div><div class="slot_title">' .$tootltip .'</div></li>';
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
		}


    //appending header section to the content
			 $htmlHeader = $htmlHeader.'<li class="arrows"><button class="arrows arrows_right"></button></li></ul></div>';
			 $html = $htmlHeader.$html.'<script type="text/javascript">       
       var sd = new Date('.$week_start_day.' * 1000);
       var sdYear = sd.getFullYear();
       sdYear = sdYear.toString().slice(2);       
       var ed = new Date('.$week_end_day.' * 1000);
       var edYear = ed.getFullYear();
       edYear = edYear.toString().slice(2);           
       $(".week_label").html("for "+(sd.getMonth()+1)+"/"+sd.getDate()+"/"+sdYear + " - " + (ed.getMonth()+1)+"/"+ed.getDate()+"/"+edYear);';
      if($loopCount == 0 || $loopCount == "" ){
        $html .= '$(".arrows").css("display","none");</script>'; 
      }else{
        $html .= '</script>';
      }	        
		   return $html;
	}

	public function getAlphaJumpTo($savedProgram='' ,$savedRole='', $savedStartDate='', $savedCompany='',$endDate='') {
		//echo $savedRole;
		ResourcePlanner::setIniFile();
		$html = "";

		$config = ResourcePlanner::getIniFile();

		$mysql = new mysqli($config->database->params->host, $config->database->params->username, $config->database->params->password, $config->database->params->dbname,$config->database->params->dbport);

    if($savedStartDate != "" && $endDate != ""){
        $start_date = $savedStartDate;
        $end_date = $endDate;
    }else{
        $start_date = date("m-d-Y", mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y'),0));
        $end_date = date("m-d-Y", mktime(1, 0, 0, date('m'), (date('d')-date('w'))+5, date('Y'),0));       
    } 

		if(($start_date != '') && ($end_date != '')){
			//$start_date_part = explode("-", $savedStartDate);
			//$end_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, $start_date_part[0], $start_date_part[1]+4, $start_date_part[2])));
			$start_date_part = explode("-", $start_date);
			$end_date_part = explode("-", $end_date);			
		}else{
			$start_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y'),0)));
			$end_date_part = explode("-", date("m-d-Y", mktime(1, 0, 0, date('m'), (date('d')-date('w'))+5, date('Y'),0)));
		}

		$start_day = mktime(0,0,0,$start_date_part[0],$start_date_part[1],$start_date_part[2],0);
		$end_day = mktime(0,0,0,$end_date_part[0],$end_date_part[1],$end_date_part[2],0);

		if($savedCompany != ""){
			$companyFilter = ' AND id IN (SELECT distinct rb.userid from resource_blocks rb, projects pj WHERE pj.company="' . $savedCompany . '" AND rb.`datestamp` >= "' . date("Y-m-d", $start_day) . ' 00:00:00" AND rb.`datestamp` <= "' . date("Y-m-d", $end_day) . ' 00:00:00" AND ((rb.`daypart` <> "9" AND rb.status is not null) OR (rb.`daypart` = "9" AND rb.hours >0)) AND rb.projectid=pj.id)';
		}else{
			$companyFilter = '';
		}
		if($savedProgram != ""){
			$programFilter = ' program = "'.$savedProgram.'" AND ';
		} else {
			$programFilter = '';
		}
                
		if($savedRole != ''){
			$pos = strpos($savedRole, "parent_");
			$subcategoryFlag = strpos($savedRole, "subcat_");
			$savedRole = str_replace("subcat_","",$savedRole);
			if($subcategoryFlag === false) {
				$flag = 'category';
			}else{
				$flag = 'subcategory';
			}

			if($pos === false){
				$roleSql = " AND  id IN (SELECT user_id FROM user_roles WHERE category_subcategory_id='$savedRole' AND `flag`='".$flag."')";  
				$sql_user = "SELECT last_name FROM `users` WHERE `company`='2' AND $programFilter `deleted`='0' " . $roleSql . " $companyFilter ORDER BY `last_name`";
			}else{
				$roleSql = " AND id IN (SELECT user_id FROM user_roles WHERE category_subcategory_id in (SELECT id FROM lnk_user_subtitles WHERE parentTitleId = '".str_replace("parent_","",$savedRole)."') AND `flag`='subcategory')";
				$sql_user = "SELECT last_name FROM `users` WHERE `company`='2' AND $programFilter `deleted`='0' " . $roleSql . " $companyFilter ORDER BY `last_name`";
			}
		}else{
			$sql_user = "SELECT last_name FROM `users` WHERE `company`='2' AND $programFilter `deleted`='0' $companyFilter ORDER BY `last_name`";
		}

		$user_res = $mysql->query($sql_user);

		if($user_res->num_rows > 0) {
			$alpha = "";
			while($user_row = $user_res->fetch_assoc()) {
				if($alpha != ucfirst(substr($user_row['last_name'], 0, 1))) {
					$html .= '<li class="jumptoItem"><span>'
						.strtolower(substr($user_row['last_name'], 0, 1)) .'</span></li>';
				}

				$alpha = ucfirst(substr($user_row['last_name'], 0, 1));
			}
			$html .= '<li class="jumptoItem"><span>All</span></li>';
		}
/*echo $sql_user;
echo $html;
exit;*/
		return $html;
	}

	public function userMonthView($userid = "") {
		ResourcePlanner::setIniFile();

		$config = ResourcePlanner::getIniFile();

		$mysql = new mysqli($config->database->params->host, $config->database->params->username, $config->database->params->password, $config->database->params->dbname,$config->database->params->dbport);

		if($_SESSION['user_id'] == $userid){
			$showSubmit = true;
		}else{
			$showSubmit = false;
		}

		$html = "";
		$week = array("mon"=>1, "tue"=>2, "wed"=>3, "thu"=>4, "fri"=>5);

		$currentMonth = date('n');
		$currentYear = date('Y');

		$numberOfDays = date('t', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
		$numberOfWeeks = ceil($numberOfDays/7);
		$firstMonthDay = date('N', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
		$lastMonthDay = date('N', mktime(0, 0, 0, $currentMonth, $numberOfDays, $currentYear));

		$date_offset_array = array("1"=>4, "2"=>3, "3"=>2, "4"=>1, "5"=>0, "6"=>0, "0"=>0,);

		$weekNum = 1;
		$row_html='';
		//$html .= '<div class="schedules_row schedules_row_wide">';
		for($d=1;$d<=$numberOfDays;$d++){
			if($d>$numberOfDays) {
				$sqlCurDay = $numberOfDays;
			} else {
				$sqlCurDay  = $d;
			}
			$currentDay = date('N', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
			$currentDateDay = date('D', mktime(0, 0, 0, $currentMonth, $d, $currentYear));

			if($d == 1 && $firstMonthDay < 6) {
				$sel_class = 'sel';
				$sqlOt = "SELECT * FROM `resource_blocks` WHERE `userid`='$userid' AND `datestamp` = '" 
				.date('Y/n/j', mktime(0, 0, 0, $currentMonth, $d, $currentYear))  ."' AND `daypart` = '9' AND `hours` > 0";
				$resOt = $mysql->query($sqlOt);
				if($resOt->num_rows > 0) {
					$otClass = "cancel";
				} else {
					$otClass = "overtime";
				}
				$row_html .= '<div class="schedules_row schedules_row_wide">
				<div class="schedule_weekof">
						<strong>'.date('M', mktime(0, 0, 0, $currentMonth, $d, $currentYear)).' '.$d.'-';
						if(($d+4)>$numberOfDays) { $row_html .= $numberOfDays; } else { $row_html .= ($d + $date_offset_array[$currentDay]); }
						$overtimeID = 'week_' .$userid .'_' .date('n-j-y', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
						$row_html .= '</strong>
						<button onclick="displayOvertime(\'' . $overtimeID . '\')" class="' .$otClass .'" id="' . $overtimeID . '"><span>+overtime</span></button>';
						$week_start_date = date('n-j-Y', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
						$week_number = date('W', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
						$returnString = self::resourceBlockLockHtml($week_start_date, $week_number, $userid, '1');
						if(stristr($returnString, 'rp_edit') === FALSE && $_SESSION['login_status']!='admin'){
							$sel_class = '';
						}
						if($showSubmit || $_SESSION['login_status']=='admin'){
							$row_html .= $returnString;
						}
						$row_html .= '</div><ul class="schedule" id="week_num_' .$week_number. '">';
				if( $firstMonthDay > 1 && $firstMonthDay <= 5 ){
					for($b=1;$b<$firstMonthDay;$b++){
						$row_html .= '<li class="schedule_day"><ul><li class="no_select"></li><li class="no_select"></li><li class="no_select"></li><li class="no_select"></li></ul></li>';
					}
				}
			}
			if($currentDay == 1 && $currentDay != $d) {
				$sel_class = 'sel';
				$sqlOt = "SELECT * FROM `resource_blocks` WHERE `userid`='$userid' AND `datestamp` = '" 
				.date('Y/n/j', mktime(0, 0, 0, $currentMonth, $d, $currentYear))  ."' AND `daypart` = '9' AND `hours` > 0";
				$resOt = $mysql->query($sqlOt);
				if($resOt->num_rows > 0) {
					$otClass = "cancel";
				} else {
					$otClass = "overtime";
				}

				$row_html .= '<div class="schedules_row schedules_row_wide">
					<div class="schedule_weekof">
						<strong>'.date('M', mktime(0, 0, 0, $currentMonth, $d, $currentYear)).' '.$d.'-';
						if(($d+4)>$numberOfDays) { $row_html .= $numberOfDays; } else { $row_html .= ($d+4); }
						$overtimeID = 'week_' .$userid .'_' .date('n-j-y', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
						$row_html .= '</strong>
						<button onclick="displayOvertime(\'' . $overtimeID . '\')" class="' .$otClass .'" id="' . $overtimeID . '"><span>+overtime</span></button>';
						$week_start_date = date('n-j-Y', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
						$week_number = date('W', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
						$returnString = self::resourceBlockLockHtml($week_start_date, $week_number, $userid, '0');
						if(stristr($returnString, 'rp_edit') === FALSE && $_SESSION['login_status']!='admin'){
							$sel_class = '';
						}
						if($showSubmit || $_SESSION['login_status']=='admin'){
							$row_html .= $returnString;
						}
						$row_html .= '</div><ul class="schedule" id="week_num_' .$week_number. '">';
			}
			if(in_array($currentDay, $week)) {
				$dayDate = date('Y/n/j', mktime(0,0,0,$currentMonth,$sqlCurDay,$currentYear));
				$dayDateDisplay = date('n/j/Y', mktime(0,0,0,$currentMonth,$sqlCurDay,$currentYear));
				$row_html .= '<!--== | START : Day ==-->
					<li class="schedule_day">
						<ul>';
						for($it = 0; $it < 8; $it++) {

							$sqlrp = "SELECT * FROM `resource_blocks` a LEFT JOIN `projects` b ON a.`projectid`=b.`id`  WHERE a.`userid`='$userid' AND a.`datestamp` = '" 
							.$dayDate  ."' AND a.`daypart` = '" .($it+1) ."' ORDER BY projectid  DESC LIMIT 0,1 ";
							$resrp = $mysql->query($sqlrp);
							$rpRow = $resrp->fetch_assoc();

							$status = "";
							$name = "";
							$tootltip = "";

							if($resrp->num_rows == 1) {
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
							}

							$row_html .= '<li class="' . $sel_class .' '. $status .'" id="userday_' .$userid ."_" .$dayDateDisplay ."_" .($it+1) .'"><div class="slot_label" style="float: left; width: 76px;" title="' . $full_name . '">' .$name .'</div><div class="slot_title">' .$tootltip .'</div>';
							if($it == 0) {
								$row_html .= '<div class="schedule_day_date" style="float: left; width: 10px;">'.$d.'</div>';
							}
							$row_html .= '</li>';
						}
						$row_html .= '</ul>
					</li>
					<!--== | END : Day ==-->';
			}
			if( $currentDay < 5 && $d != $numberOfDays ){ $row_html .=  ""; }
//				if( $currentDay == 7 && $row_html!='') {
			if(("fri" == strtolower($currentDateDay)) || ($weekNum == $numberOfWeeks && $d==$numberOfDays)){
				$weekNum += 1;
				$row_html .= '</ul></div>
				<!--== | END: Row | ==-->';
				$html.=$row_html;
				$row_html='';
			}
			// $html.=$row_html;
		}
		$html.=$row_html;
//			$html .= '</div>';

		$weekday = 1;

		return $html;
	}
	function resourceBlockLockHtml($date, $week, $user, $first_week_flag){
		$date_array = explode("-", $date);
		$resourceBlockSql = 'SELECT 1 from `resource_planner_lock` WHERE `user_id`="' .$user. '" AND `week_num`="' .$week. '" AND `year`="' .$date_array[2]. '" AND `active`="1"';
		$result = self::getQuery($resourceBlockSql);

		$offset = array("1" => '0', "2" => '1', "3" => '2', "4" => '3', "5" => '4');
		if($first_week_flag == '1'){
			$start_day = date('w', mktime(0, 0, 0, $date_array[0], $date_array[1], $date_array[2],0));
			$start_date = date('Y-n-j', mktime(0, 0, 0, $date_array[0], $date_array[1]-$offset[$start_day], $date_array[2],0));
		}else{
			$start_date = date('Y-n-j', mktime(0, 0, 0, $date_array[0], $date_array[1], $date_array[2],0));
		}

		if(count($result) == 0){		
			$html = '<button class="rp_edit" id="lock_week_'.$week.'" onclick="completeWeek(\'' .$start_date. '\', \'' .$week. '\', \'' .$user. '\',\''.$_SESSION['login_status']. '\')"><span>Submit</span></button>';			
		}else{
			if($_SESSION['login_status']=='admin')
			{
				$html = '<button class="rp_edit" id="lock_week_'.$week.'" onclick="unSubmitWeek(\'' .$start_date. '\', \'' .$week. '\', \'' .$user. '\',\''.$_SESSION['login_status']. '\')"><span>Un Submit</span></button>';
			}
			else
			{
				$html = '<button class="rp_complete" ><span>Submit</span></button>';
			}
		}
		return $html;
	}

	function getMonthdropdown($intCurrMonth,$year){
		$html = '';
		for($yearcnt = 2009;$yearcnt<=$year;$yearcnt++)
		{
			if($yearcnt==$year){
				$intMonth = $intCurrMonth ;
			}
			else{
				$intMonth = 12;
			}
			for($counter = 1; $counter <= $intMonth; $counter++)
			{											
				$timestamp = mktime(0, 0, 0, $counter, 1, $yearcnt); 
				$html .=  "<option value='".DATE("m", $timestamp)."/".$yearcnt."'>".DATE("F", $timestamp)." ".$yearcnt."</option>";
			}
		}
		return $html;
	}

	function fullProgramListHTML($savedProgram){
		$html = '';
		$programList = self::getQuery("SELECT id,program from lnk_programs order by program");
		foreach($programList as $key=>$value){
			if($savedProgram == $value['id']){
				$html.= '<option value="'.$value['id'].'" selected>'.$value['program'].'</option>';
			} else {
				$html.= '<option value="'.$value['id'].'">'.$value['program'].'</option>';
			}
		}
		return $html;
	}
}
