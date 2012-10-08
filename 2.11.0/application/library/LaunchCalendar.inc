<?PHP
	error_reporting(0);
	/*
		Control Tower classes parent and child
	*/
	class LaunchCalendar {
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
		/*
			Work Orders Setters
		*/
		/*
			Work Orders Getters
		*/
	}
	class LcDisplay extends LaunchCalendar {
		private $proj_height;
		private $date_width;
		private $start_pad;
		
		public function getQuery($query) {
			/*LaunchCalendar::setIniFile();
			
			$config = LaunchCalendar::getIniFile();
			
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
		public function getTotalProjectHeight() {
			return $this->proj_height;
		}
		public function getDaysInMonth($month, $year) {
			$daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	        if ($month < 1 || $month > 12)
	        {
	            return 0;
	        }
			
	        $d = $daysInMonth[$month - 1];
	   
	        if ($month == 2)
	        {
	            // Check for leap year
	            // Forget the 4000 rule, I doubt I'll be around then...
	        
	            if ($year%4 == 0)
	            {
	                if ($year%100 == 0)
	                {
	                    if ($year%400 == 0)
	                    {
	                        $d = 29;
	                    }
	                }
	                else
	                {
	                    $d = 29;
	                }
	            }
	        }
	    
	        return $d;
	    }
		public function getProjectHeight($projId) {
			$min_rows = 5;
			$min_height = 76; //in pixels
			/*$individual_height = $min_height / $min_rows;
			$returned_height = 0;
			
			$phases = self::getQuery("SELECT * FROM `project_phases` a, `lnk_project_phase_types` b WHERE a.`project_id`='$projId' AND a.`phase_type` = b.`id` AND b.`launch_calendar_flag`='1';");
			
			if(sizeof($phases) > $min_rows) {
				$returned_height = $min_height + ((sizeof($phases)-$min_rows) * $individual_height);
			} else {
				$returned_height = $min_height;
			}*/

			$returned_height = $min_height;
			
			return $returned_height;
		}
		public function getWidth() {
			$start_date = self::getQuery("SELECT a.`start_date` FROM `project_phases` a, `projects` b, `lnk_project_phase_types` c WHERE a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`start_date`) > 0 AND c.`launch_calendar_flag`='1' AND a.`phase_type`=c.`id` ORDER BY a.`start_date` ASC LIMIT 1");
			$end_date = self::getQuery("SELECT a.`projected_end_date` FROM `project_phases` a, `projects` b, `lnk_project_phase_types` c WHERE a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`projected_end_date`) > 0 AND c.`launch_calendar_flag`='1' AND a.`phase_type`=c.`id` ORDER BY a.`projected_end_date` DESC LIMIT 1");
			
			$start_date_time_part = explode(" ", $start_date[0]['start_date']);
			$start_date_part = explode("-", $start_date_time_part[0]);
			$start_time_part = explode(":", $start_date_time_part[1]);
			
			
			$end_date_time_part = explode(" ", $end_date[0]['projected_end_date']);
			$end_date_part = explode("-", $end_date_time_part[0]);
			$end_time_part = explode(":", $end_date_time_part[1]);
			
			$days_in_between = self::dateDiff("-", $end_date_time_part[0], $start_date_time_part[0]);
			
			$this->date_width = $days_in_between * 31 + 215;
			return $this->date_width;
		}
		/*public function getStartScroll() {
			$start_date = self::getQuery("SELECT a.`start_date` FROM `project_phases` a, `projects` b WHERE a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`start_date`) > 0 ORDER BY a.`start_date` ASC LIMIT 1");
			$end_date = self::getQuery("SELECT a.`projected_end_date` FROM `project_phases` a, `projects` b WHERE a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`projected_end_date`) > 0 ORDER BY a.`projected_end_date` DESC LIMIT 1");
			
			$start_date_time_part = explode(" ", $start_date[0]['start_date']);
			$start_date_part = explode("-", $start_date_time_part[0]);
			$start_time_part = explode(":", $start_date_time_part[1]);
			
			
			$end_date_time_part = explode(" ", $end_date[0]['projected_end_date']);
			$end_date_part = explode("-", $end_date_time_part[0]);
			$end_time_part = explode(":", $end_date_time_part[1]);
			
			$cur_date = date("Y-m-d");
			$cur_date_part = explode("-", $cur_date);
			
			if($cur_date_part[0] <= $end_date_part[0] && $cur_date_part[1] <= $end_date_part[1] && $cur_date_part[2] <= $end_date_part[2]) {
				$dif = self::dateDiff("-", $cur_date, $start_date_time_part[0]);
			} else {
				$dif = 0;
			}
			
			*/
		public function dateDiff($dformat, $endDate, $beginDate) {
		    $date_parts1=@explode($dformat, $beginDate);
		    $date_parts2=@explode($dformat, $endDate);
			//m,d,y
		    $start_date=@gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
		    $end_date=@gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
			 
			$diff = $end_date - $start_date;
			if($diff < 0) {
				$diff *= -1;
			}
			
		    return $diff;
	    }
		public function getCompanyHTML() {
			$companies = self::getQuery(QRY_COMPANIES_ASC);
			$html = "";
			
			$html .= '<select id="client_filter" name="client_filter" onChange="lcFilter();">'
				.'<option value="">Select a Client</option>';
			for($i = 0; $i < sizeof($companies); $i++) {
				$html .= '<option value="' .$companies[$i]['id'] .'">' .$companies[$i]['name'] .'</option>';
			}
			$html .= '</select>';
			
			return $html;
		}
		public function getUsersHTML() {
			$users = self::getQuery("select * from users u where u.id in (SELECT distinct user_id FROM `project_roles` WHERE `resource_type_id`='2')");
			$html = "";
			
			$html .= '<select id="producer_filter" name="producer_filter" onChange="lcFilter();">'
				.'<option value="">Select an Engagement Lead</option>';
			for($i = 0; $i < sizeof($users); $i++) {
				$html .= '<option value="' .$users[$i]['id'] .'">' 
					.ucfirst($users[$i]['first_name']) .' ' 
					.ucfirst($users[$i]['last_name'])
					.'</option>';
			}
			$html .= '</select>';
			
			return $html;
		}
		public function getProjectsHTML() {
			if($_SESSION['login_status'] == "client") {
				$company_query = " AND `company`='" .$_SESSION['company'] ."'";
			} else {
				$company_query = "";
			}
			$projects = self::getQuery("SELECT * FROM `projects` WHERE `archived` = '0' AND `active` = '1' AND `deleted` = '0'$company_query ORDER BY `project_name` ASC");
			$html = "";
			$this->proj_height = 0;
			
			$html .= '<ul id="project_list" class="lc_calendar_column_1">';
			for($i = 0; $i < sizeof($projects); $i++) {	
				//$producer_user = array();
				//Select the producer role
				$proj_roles = self::getQuery("SELECT * FROM `project_roles` pr,users u WHERE `project_id`='" .$projects[$i]['id'] ."' AND `resource_type_id`='2' and pr.user_id = u.id LIMIT 1");
				/*if(!empty($proj_roles[0]['user_id'])) {
					$producer_user = self::getQuery("SELECT * FROM `users` WHERE `id`='" .$proj_roles[0]['user_id'] ."'");
				}
				
				if($_SESSION['login_status'] == "client") {
					
				} else {
					
				}*/
				
				$this->proj_height += self::getProjectHeight($projects[$i]['id']);
				$html .= '
				<!-- Project -->
				<li id="' .$projects[$i]['company'] .'_' .@$proj_roles[0]['user_id'] .'" style="height: ' .self::getProjectHeight($projects[$i]['id']) .'px;">
					<dl>
						<dt>';
						if($_SESSION['login_status'] != "client") {
							$html .= '<a href="/controltower/index/edit/?project_id=' .$projects[$i]['id'] .'">';
						}
						
						if(strlen(($projects[$i]['project_code'] .' - ' .$projects[$i]['project_name']))>34) {$html .= '<span title="'.$projects[$i]['project_name'].'">'.substr(($projects[$i]['project_code'] .' - ' .$projects[$i]['project_name']), 0, 34).'...</span>';}
						else {$html .= $projects[$i]['project_code'] .' - ' .$projects[$i]['project_name'];}
						
						if($_SESSION['login_status'] != "client") {
							$html .= '</a>';
						}
						
						$html .= '</dt>
						<dd>' .@ucfirst($proj_roles[0]['first_name']) .' ' .@ucfirst($proj_roles[0]['last_name']) .'</dd>
						<!--<dd>Producer 2</dd>-->
						<!--dd>Technical Proj. Manager</dd-->
					</dl>
				</li>';
			}
			$html .= "</ul>";
			
			return $html;
		}

		public function getStagesHTML() {
			if($_SESSION['login_status'] == "client") {
				$company_query = " AND `company`='" .$_SESSION['company'] ."'";
			} else {
				$company_query = "";
			}

			$rp_start_date = date("Y").'-01-01';
			$rp_end_date = date("Y").'-12-31';

			//self::getWeeksHTML();
			
			$projects = self::getQuery("SELECT * FROM `projects` WHERE `archived` = '0' AND `active` = '1' AND `deleted` = '0'$company_query ORDER BY `project_name` ASC");
			$html = "";
			$this->proj_height = 0;
			
			$start_date = self::getQuery("SELECT a.`start_date` FROM `project_phases` a, `projects` b, `lnk_project_phase_types` c WHERE a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`start_date`) > 0 AND c.`launch_calendar_flag`='1' AND a.`phase_type`=c.`id` ORDER BY a.`start_date` ASC LIMIT 1");

			/*$end_date = self::getQuery("SELECT a.`projected_end_date` FROM `project_phases` a, `projects` b, `lnk_project_phase_types` c WHERE a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`projected_end_date`) > 0 AND c.`launch_calendar_flag`='1' AND a.`phase_type`=c.`id` ORDER BY a.`projected_end_date` DESC LIMIT 1");*/

			$start_date_time_part = explode(" ", $start_date[0]['start_date']);
			$start_date_part = explode("-", $start_date_time_part[0]);
			$start_time_part = explode(":", $start_date_time_part[1]);
			
			
			/*$end_date_time_part = explode(" ", $end_date[0]['projected_end_date']);
			$end_date_part = explode("-", $end_date_time_part[0]);
			$end_time_part = explode(":", $end_date_time_part[1]);*/
			
			$this->start_pad = $start_date_part[2];

			$project_rates =  self::getQuery("Select tab1.projectid,tab2.phase as phase, sum(tab1.Total*tab2.rate) AS project_amount from (Select tab3.projectid, tab3.userid, sum(tab3.Total) AS Total from (Select rb.projectid, rb.userid, count(1) AS Total  from projects pj, resource_blocks rb where pj.id=rb.projectid and pj.active='1' and pj.deleted='0' and pj.archived='0'  and rb.datestamp >= '".$rp_start_date."' and rb.datestamp <= '".$rp_end_date."' and rb.status='4' and rb.daypart <> 9 group by pj.id, rb.userid UNION Select rb.projectid, rb.userid, rb.hours AS Total  from projects pj, resource_blocks rb where pj.id=rb.projectid and pj.active='1' and pj.deleted='0' and pj.archived='0' and rb.datestamp >= '".$rp_start_date."' and rb.datestamp <= '".$rp_end_date."' and rb.status='4' and rb.daypart = 9) tab3 group by tab3.projectid, tab3.userid) tab1, (select upr.project_id , upr.phase_subphase_id AS phase, upr.user_id, ppf.rate from user_project_role upr, project_phase_finance ppf where upr.project_id=ppf.project_id and upr.phase_subphase_id=ppf.phase and upr.flag='phase' UNION select upr.project_id , pspf.phase AS phase, upr.user_id, pspf.rate from user_project_role upr, project_sub_phase_finance pspf where upr.project_id=pspf.project_id and upr.phase_subphase_id=pspf.sub_phase and upr.flag='subphase' UNION Select rb.projectid, ppf.phase AS phase, rb.userid, ppf.rate from resource_blocks rb, users us, project_phase_finance ppf WHERE rb.userid = us.id AND rb.projectid = ppf.project_id AND  us.role=ppf.phase and rb.datestamp >= '".$rp_start_date."' and rb.datestamp <= '".$rp_end_date."' and rb.userid NOT IN (SELECT DISTINCT user_id from user_project_role upr where upr.project_id = rb.projectid) group by rb.projectid, rb.userid) tab2 WHERE tab1.projectid = tab2.project_id and tab1.userid = tab2.user_id group by tab1.projectid,tab2.phase ");

			$total_project_rates =  self::getQuery("select project_id , phase, sum(Total) as total_amount from ( select ppf.project_id , ppf.phase, (hours * rate) as Total from  `project_phases` pp ,`project_phase_finance` ppf where pp.project_id = ppf.project_id and pp.phase_type = ppf.phase UNION select pp.project_id,pp.phase_type,sum((hours*rate)) as Total from project_sub_phase_finance pspf,`project_phases` pp where  pp.project_id = pspf.project_id and pp.phase_type = pspf.phase group by pspf.project_id , pspf.phase) tab1 group by project_id , phase ");

			$project_rate_array;
			for($i = 0;  $i < sizeof($project_rates); $i++ )
			{
				$project_rate= $project_rates[$i];
				$project_rate_array[$project_rate["projectid"]][$project_rate["phase"]] = $project_rate["project_amount"];
			}	
			
			$total_rate_array;
			for($i = 0;  $i < sizeof($total_project_rates); $i++ )
			{
				$total_rate= $total_project_rates[$i];
				$total_rate_array[$total_rate["project_id"]][$total_rate["phase"]] = $total_rate["total_amount"];
			}	

			$day_width = self::getWidth();

			$html .= '<ul id="project_calendar"  class="lc_calendar_column_2" onscroll="updateScroll();">';
			for($i = 0; $i < sizeof($projects); $i++) {	
				$proj_phases = self::getQuery("SELECT a.project_id as project_id, a.phase_type as phase_type, start_date, projected_end_date,b.name as name FROM `project_phases` a, `lnk_project_phase_types` b,project_phase_finance c WHERE a.`project_id`='" .$projects[$i]['id'] ."' AND a.`phase_type` = b.`id` AND c.`project_id` = a.`project_id` AND a.`phase_type` = c.`phase` AND b.`launch_calendar_flag`='1';");
				//Select the producer role
				$proj_roles = self::getQuery("SELECT * FROM `project_roles` WHERE `project_id`='" .$projects[$i]['id'] ."' AND `resource_type_id`='2' LIMIT 1");
				
				//$start_date2 = self::getQuery("SELECT a.`start_date` FROM `project_phases` a, `projects` b WHERE b.`id`='" .$projects[$i]['id'] ."' AND a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`start_date`) > 0 ORDER BY a.`start_date` ASC LIMIT 1");
				$start_date2 = self::getQuery("SELECT a.`start_date` FROM `project_phases` a, `projects` b, `lnk_project_phase_types` c WHERE b.`id`='" .$projects[$i]['id'] ."' AND  a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`start_date`) > 0 AND c.`launch_calendar_flag`='1' AND a.`phase_type`=c.`id` ORDER BY a.`start_date` ASC LIMIT 1");
				//$end_date2 = self::getQuery("SELECT a.`projected_end_date` FROM `project_phases` a, `projects` b WHERE b.`id`='" .$projects[$i]['id'] ."' AND a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`projected_end_date`) > 0 ORDER BY a.`projected_end_date` DESC LIMIT 1");
				$end_date2 = self::getQuery("SELECT a.`projected_end_date` FROM `project_phases` a, `projects` b, `lnk_project_phase_types` c WHERE b.`id`='" .$projects[$i]['id'] ."' AND  a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`projected_end_date`) > 0 AND c.`launch_calendar_flag`='1' AND a.`phase_type`=c.`id` ORDER BY a.`projected_end_date` DESC LIMIT 1");
				//echo "SELECT a.`projected_end_date` FROM `project_phases` a, `projects` b WHERE b.`id`='" .$projects[$i]['id'] ."' AND a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`projected_end_date`) > 0 ORDER BY a.`projected_end_date` DESC LIMIT 1";
				
				$start_date_time_part2 = @explode(" ", $start_date2[0]['start_date']);
				$start_date_part2 = @explode("-", $start_date_time_part2[0]);
				$start_time_part2 = @explode(":", $start_date_time_part2[1]);
				
				
				$end_date_time_part2 = @explode(" ", $end_date2[0]['projected_end_date']);
				$end_date_part2 = @explode("-", $end_date_time_part2[0]);
				$end_time_part2 = @explode(":", $end_date_time_part2[1]);
				
				$days_in_between = self::dateDiff("-", $start_date_time_part2[0], $start_date_time_part[0]);				
				$html .= '
				<!-- Project -->
				<li id="' .$projects[$i]['company'] .'_' .@$proj_roles[0]['user_id'] .'" style="height: ' .self::getProjectHeight($projects[$i]['id']) .'px; width: ' .$day_width .'px" >';
				
				if(sizeof($proj_phases) > 0 && isset($start_date2[0]['start_date']) && isset($end_date2[0]['projected_end_date'])) {
					if($days_in_between < 0) {
						$days_in_between = 0;
					}
					
					$total_budget = 0;
					$total_burn = 0;
					$total_percent = 0;
					
					/*$rp_time_total = self::getQuery("SELECT * FROM `resource_blocks` WHERE `projectid`='" .$projects[$i]['id'] ."'");
					$total_finance = self::getQuery("SELECT * FROM `project_phase_finance` WHERE `project_id`='" .$projects[$i]['id'] ."'");
					
					for($r = 0; $r < sizeof($rp_time_total); $r++) {
						$select_user_rate = self::getQuery("SELECT b.`id` FROM `users` a, `lnk_project_phase_types` b WHERE a.`id`='" .$rp_time_total[$r]['userid'] ."' AND b.`id`=a.`role` AND b.`launch_calendar_flag`='1' LIMIT 1");
						$select_fin_rate = self::getQuery("SELECT * FROM `project_phase_finance` WHERE `phase`='" .@$select_user_rate[0]['id'] . "' AND `project_id`='" .@$projects[$i]['id'] ."' LIMIT 1");
						if(isset($select_fin_rate[0]['rate'])) {
							if($rp_time_total[$r]['hours'] > 0) {
								$total_burn += $rp_time_total[$r]['hours']*$select_fin_rate[0]['rate'];
							} else {
								$total_burn += 2*$select_fin_rate[0]['rate'];
							}
						}
					}
					for($f = 0; $f < sizeof($total_finance); $f++) {
						if(isset($total_finance[$f]['hours']) && isset($total_finance[$f]['rate'])) {
							$total_budget += $total_finance[$f]['hours']*$total_finance[$f]['rate'];
						}
					}*/
					
					for($f = 0; $f < sizeof($proj_phases); $f++){
						$total_burn += $project_rate_array[$proj_phases[$f]["project_id"]][$proj_phases[$f]["phase_type"]];
						$total_budget += $total_rate_array[$proj_phases[$f]["project_id"]][$proj_phases[$f]["phase_type"]];
					}

					if($total_budget > 0 && $total_burn > 0) {
						$total_percent = $total_budget/$total_burn;
					}
					
					$days_diff_project = @self::dateDiff("-", $end_date_time_part2[0], $start_date_time_part2[0]);
					
					$total_width = (($days_diff_project*31) + 31);
					$total_percent_width = $total_width * $total_percent;
					
					if($total_percent_width > $total_width) {
						$total_percent_width = $total_width;      
					}
					
					$html .= '<dl class="lc_timeline_container" style="left: ' .(($days_in_between*31) + (($this->start_pad - 1)*31)).'px; width: ' .$total_width .'px; height: ' .self::getProjectHeight($projects[$i]['id']) .'px;">
						<div class="lc_timeline_all" style="width: ' .$total_percent_width .'px; height: ' .self::getProjectHeight($projects[$i]['id']) .'px;"></div>';
						
						for($x = 0; $x < sizeof($proj_phases); $x++) {
							/*$phase = self::getQuery("SELECT * FROM `lnk_project_phase_types` WHERE `id`='" .$proj_phases[$x]['phase_type'] ."' AND `launch_calendar_flag`='1' LIMIT 1");*/
							
							$proj_phase_start_date_part = explode(" ", $proj_phases[$x]['start_date']);
							$proj_phase_end_date_part = explode(" ", $proj_phases[$x]['projected_end_date']);
							
							$proj_date_dif = @self::dateDiff("-", $proj_phase_start_date_part[0], $start_date_time_part2[0]);
							if(isset($proj_phase_start_date_part[0]) && $proj_date_dif < $total_width) {
								$phase_left = $proj_date_dif*31;
							} else {
								$phase_left = 0;
							}
							
							$phase_width_diff = @self::dateDiff("-", $proj_phase_end_date_part[0], $proj_phase_start_date_part[0]);
							
							if( ($phase_width_diff*31) < $total_width) {
								$phase_width = $phase_width_diff*31;
							} else {
								$phase_width = 5;
							}
							
							/*$users = self::getQuery("SELECT * FROM `users` WHERE `role`='" .$phase[0]['id'] ."'");*/
							
							$total_hours = 0;
							/*for($uLoop = 0; $uLoop < sizeof($users); $uLoop++) {
								$rp_time = self::getQuery("SELECT * FROM `resource_blocks` WHERE `userid`='" .$users[$uLoop]['id'] ."' AND  `projectid`='" .$projects[$i]['id'] ."'");
								
								for($rpLoop = 0; $rpLoop < sizeof($rp_time); $rpLoop++) {
									$get_rate = self::getQuery("SELECT * FROM `project_phase_finance` WHERE `phase`='" .$phase[0]['id'] ."' AND `project_id`='" .$projects[$i]['id'] ."' LIMIT 1");
									if(isset($get_rate[0]['rate'])) {
										if($rp_time[$rpLoop]['hours'] > 0) {
											$total_hours += $rp_time[$rpLoop]['hours']*$get_rate[0]['rate'];
										} else {
											$total_hours += 2*$get_rate[0]['rate'];
										}
									}
								}
							}*/
							$total_hours = $project_rate_array[$proj_phases[$x]["project_id"]][$proj_phases[$x]["phase_type"]];

							/*$total_phase_hours_in_project = self::getQuery("SELECT * FROM `project_phase_finance` WHERE `phase`='" .$phase[0]['id'] ."' AND `project_id`='" .$projects[$i]['id'] ."'");*/


							$total_phase_hours = 0;
							/*for($phLoop = 0; $phLoop < sizeof($total_phase_hours_in_project); $phLoop++) {
								$total_phase_hours += ($total_phase_hours_in_project[$phLoop]['hours']*$total_phase_hours_in_project[$phLoop]['rate']);
							}*/
							$total_phase_hours = $total_rate_array[$proj_phases[$x]["project_id"]][$proj_phases[$x]["phase_type"]];

							if($total_phase_hours != 0 && $total_hours != 0) {
								$base_percent = $total_hours/$total_phase_hours;
							} else {
								$base_percent = 0;
							}
							//Remove this when data is in the resource blocks for resource planner
							//Then check the calculations
							//$base_percent = .77;
							$phase_burn_width = $phase_width*$base_percent;
							
							if($phase_burn_width > $phase_width) {
								$phase_burn_width = $phase_width;
							}						
							
							$html .= '<dd>
									<div class="lc_timeline_single" style="width: ' .($phase_width+31) .'px; left: ' .$phase_left .'px;">
										<span>' .$proj_phases[$x]['name'] .'</span>
										<div class="lc_timeline_burn" style="width: ' .$phase_burn_width .'px;"></div>
									</div>
								</dd>';
							}
						
					$html .='</dl>';
				}
				
				$html .'</li>';
			}
			$html .= '</ul>';
			
			return $html;
		}
		public function getWeeksHTML() {
			$monthNames = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");
			$html = "";
			$cur_date = date("Y-F-d");
			$cur_date_part = @explode("-", $cur_date);

			$current_date = strtotime($cur_date); 
			
			$start_date = self::getQuery("SELECT a.`start_date` FROM `project_phases` a, `projects` b, `lnk_project_phase_types` c WHERE a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`start_date`) > 0 AND c.`launch_calendar_flag`='1' AND a.`phase_type`=c.`id` ORDER BY a.`start_date` ASC LIMIT 1");
			$end_date = self::getQuery("SELECT a.`projected_end_date` FROM `project_phases` a, `projects` b, `lnk_project_phase_types` c WHERE a.`project_id` = b.`id` AND b.`archived` = '0' AND b.`active` = '1' AND b.`deleted` = '0' AND YEAR(a.`projected_end_date`) > 0 AND c.`launch_calendar_flag`='1' AND a.`phase_type`=c.`id` ORDER BY a.`projected_end_date` DESC LIMIT 1");
			
			$start_date_time_part = explode(" ", @$start_date[0]['start_date']);
			$start_date_part = explode("-", @$start_date_time_part[0]);
			$start_time_part = explode(":", @$start_date_time_part[1]);
			
			
			$end_date_time_part = explode(" ", @$end_date[0]['projected_end_date']);
			$end_date_part = explode("-", @$end_date_time_part[0]);
			$end_time_part = explode(":", @$end_date_time_part[1]);
			
			$this->start_pad = $start_date_part[2];
			
			if(isset($start_date_part[2]) && $start_date_part[2] > 0) {
				$start_pad = floor(7/$start_date_part[2]);
			} else {
				$start_pad = 0;
			}
			
			$html .='<div class="lc_weeks_container" style="width: ' .self::getWidth() .'px">';
			$next_sub = 0;	
			for($i = $start_date_part[0]; $i <= $end_date_part[0]; $i++) {
				if($start_date_part[0] == $i) { 
					$m = $start_date_part[1];
					$m=intval($m);
				} else {
					$m = 1;
				}
				
				for($m; $m <= 12; $m++) {
					$days = self::getDaysInMonth($m, $i);
					
					$weeks = ceil($days / 7);
					
					for($w = 0; $w < $weeks; $w++) {
						$last_week = false;
						if($next_sub < 0) {
							$next_sub = 0;
						}
						
						if($days >= ((1+($w*7))+$next_sub)) {
							$start_day = ((1+($w*7))+$next_sub);
						} else {
							$start_day = ((1+($w*7))+$next_sub-$days);
						}

						$weekday  = strtotime($i."-".$m."-". $start_day);
					
						if( $current_date >= $weekday && $current_date < ($weekday+691200 )) {
							$id = ' id="current_date"';
						} else {  
							$id = '';
						}
						
						$html .= '<!-- Week -->
							<div class="lc_week_single">
								<div class="lc_week_title"' .$id .'>' .$monthNames[$m] . ' ' .$start_day .', ' .$i  .'</div>
								<div class="lc_days_container">';
									
									$last_day = 1;
									for($d = 1; $d <= 7; $d++) {
										
										if($d == 7) {
											$calss = ' class="last_day"';
										} else {
											$calss = '';
										}
										
										if($days >= (($d+($w*7)) + $next_sub)) {
											$html .= '<span' .$calss .'>' .(($d+($w*7)) + $next_sub) .'</span>';
											$last_day = $d;
											if($days == (($d+($w*7)) + $next_sub)) {
												$last_week = true;
												if($d == 7) {
													$next_sub = 0;
												}
											}
										} else {
											$html .= '<span' .$calss .'>' .((($d+($w*7)) +$next_sub) - $days) .'</span>';
											if($d==7) {
												$next_sub = 7 - $last_day;
												$last_week = true;
											}
										}
									}
									
									$html .= '</div>
							</div>';
						if($last_week) {
							$w = $weeks;
							break;
						}
					}
					
				}
			}
				
			$html .= '</div>';
			
			return $html;
		}
	}
?>