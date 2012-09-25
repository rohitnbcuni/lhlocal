<?php
//modified WW 2/3/09 bug  WO: RP CSS Broken

	include('ResourcePlanner.inc');
	//include("fckeditor.php");

	class Resourceplanner_IndexController extends LighthouseController 
	{
	    public function indexAction()
		{
			$currweek = DATE("W");
			$year = DATE("Y");
			$intCurrMonth = DATE("m");
			$userID = @$_REQUEST["userid"];
			
			

			if($_SESSION['login_status'] == "admin" || $_SESSION['user_id'] == $userID){
				$display_style = '';
			}else{
				$display_style = ' style="display:none; "';
			}

			if ($userID) {
				/* LH fixes
				 * LH#21355
				 */
					
				if(!is_numeric($userID)){
					$this->_redirect("noaccess");
				}
				$user_data = RpDisplay::getUserInfo($userID);
				$user_data = trim($user_data);
				if(empty($user_data)){
					$this->_redirect("noaccess");
				}
				//LH# 33559 change height:1055px to 0px	
				echo '<!--=========== START: COLUMNS ===========-->
				<div id="rp_content" style="padding-top:1px;background:white;height:0px"><div id="dimmer_rp" class="popHours blur message_lock_confirm " style="width:3000px;height:1024px;position:fixed;background-color:#ffffff;z-index:1;margin-top:0px;margin-left:-630px;opacity: 0.3; filter: alpha(opacity = 30); zoom:1; z-index:1"></div>
				<div class="rp_report popHeader " style="position: fixed; background-color: rgb(255, 255, 255); opacity: 0.3;filter: alpha(opacity = 30); display: none; margin-right: -300px; height: 1024px; width: 3000px; z-index: 1;"></div>
					<div class="column_main_resource_noborder">
					<form method="post" action="' .BASE_URL .'/_ajaxphp/export_rp_excel.php" target="_blank" id="excelform1" name="excelform1">
						</form>
						<form method="get" action="" target="_blank" name="excelform" id="excelform" onSubmit="return false;">
						<div class="rp_report">
							<div class="close_rp_report">X</div>
							<div class="rp_report_content">
								<ul>
									<li class="label_name">Select Month</li>
									<li class="label_value"> 
										<select class="month_list" id="month_list">								
										'.RpDisplay::getMonthdropdown($intCurrMonth,$year).'
										</select>
									</li>
								</ul>
								<div class="add">
									<button onClick="generateReport()"><span>Generate</span></button>
								</div>
							</div>
						</div>
						</form>
						<!--==| START: Bucket |==-->
						<div class="main_actions main_actions_resource">
							<button class="monitors" id="week_loading"><span>Week loading: 0%</span></button>

							<button class="monitors" id="quarter_loading"><span>Quarter loading: 0%</span></button>
							<button class="monitors" id="year_loading"><span>Year loading: 0%</span></button>';
							if($_SESSION['login_status'] == "admin"){
								echo '<button onClick="$(\'.rp_report\').css({display:\'block\'}); return false;"><span>run report</span></button>';
							}
						echo '</div>
						<!--==| END: Bucket |==-->

						<!--==| START: Bucket |==-->
						<div class="title_med">

							<div class="left_actions">
								<button id="all_resources" class="back_arrow"><span>All Resources</span></button>
								<h4>Displaying: '.RpDisplay::getUserInfo($userID).'</h4>
							</div>
							<div class="right_actions datePick">
								<label class="small">Jump to Date</label>
								<input type="text" value="--" id="basics" class="single_user_date_jump" />

							</div>
						</div>
						<!--==| END: Bucket |==-->

						<!--==| START: Bucket |==-->
						<div class="month_controller_container">
							<ul class="month_controller">
								<li class="month_arrows"><button class="month_arrows month_arrows_left"></button></li>
								<li class="month_controller_display"></li>
								<li class="month_arrows"><button class="month_arrows month_arrows_right"></button></li>
							</ul>
						</div>
						<!--==| END: Bucket |==-->

						<!--==| START: Bucket |==-->
						<div class="resources_controller resources_controller_wide">
							<ul class="days_container days_container_wide">
								<li>Monday</li>
								<li>Tuesday</li>
								<li>Wednesday</li>
								<li>Thursday</li>
								<li>Friday</li>
							</ul>
						</div>
						<!--==| END: Bucket |==-->


						<!--==| START: Bucket |==-->
						<div class="month_container">
						<div class="schedules_controller">
							' .RpDisplay::userMonthView($userID);
							echo '<br />
						</div>
						</div>
						<!--==| END: Bucket |==-->
						</div>
					<!--=========== COLUMN BREAK ===========-->
					<div class="column_right_resource">

						<!--==| START: Bucket |==-->
						<div class="title_med allocation_expanded"><h4>Allocation Type</h4></div>
						<div class="allocation_type">
							<ul>
								<li class="key_deselect">De-select current selection</li>
								<li class="key_overhead" ' . $display_style . '>Overhead / Internal</li>
								<li class="key_outoffice" ' . $display_style . '>Out of Office</li>
								<li class="key_blank">Unassigned</li>
								<li class="key_convertbillable" ' . $display_style . '>Convert hours to Actual</li>
							</ul>
						</div>
						<!--==| END: Bucket |==-->

						<!--==| START: Bucket |==-->
						<div class="title_med">
							<h4>Hours</h4>
							<div class="right_actions right_actions_small">';
						if($_SESSION['login_status'] == "admin"){
							echo '<input name="hours_type" class="hours_type" id="hours_type_scheduled" type="radio" value="scheduled" /><label class="small">Scheduled</label>
								<input name="hours_type" class="hours_type" id="hours_type_actual" type="radio" value="actual" CHECKED /><label class="small">Actual</label>';
						}else if($_SESSION['user_id'] == $userID){
							echo '<input name="hours_type" class="hours_type" id="hours_type_scheduled" type="radio" value="scheduled" /><label class="small">Scheduled</label><input name="hours_type" class="hours_type" id="hours_type_actual" type="radio" value="actual" CHECKED /><label class="small">Actual</label>';
						}else{
							echo '<input name="hours_type" class="hours_type" id="hours_type_scheduled" type="radio" value="scheduled" CHECKED /><label class="small">Scheduled</label>';
						}
						echo '</div>
						</div>
						<div class="hours">
							<ul>' . RpDisplay::projectListHTML() .'</ul>
						</div>
						<!--==| END: Bucket |==-->
				</div>
				<!--=========== END: COLUMNS ===========-->
				</div>

				<div id="tooltip" class="rp_block_tooltip"></div>
				<div id="blur" class="blur jHelperTipClose"></div>

				<div id="popHours" class="popHours" style="display: none; position: absolute; top: 0px; left: 0px; z-index: 999;">
					<div class="popHeader"></div>
					<div class="popMain">
						<table>
						<tr>
							<td>
								<label>Projects:</label>
								<select name="overtime_project" id="overtime_project" style="width: 290px;">
									<option value="">--Select Project--</option>
									'.RpDisplay::fullProjectListHTML().'
								</select>
							</td>
						</tr>
						<tr>
							<td width="80">
								<label>Hours:</label><br />
								<select name="overtime_hours" id="overtime_hours"style="width: 57px;">';
								for($i=0;$i<=40;$i++)
								{
									echo '<option id="hours'.$i.'" value="'.$i.'">'.$i.'</option>';
								}
						echo '</select>
							</td>
						</tr>
						<tr>
							<td>
								<label>Notes:</label>
								<textarea name="overtime_notes" id="overtime_notes" style="width: 290px; height: 48px"></textarea>
							</td>
						</tr>
						<tr>
							<td>
								<button id="saveBtn" style="float: right;"><span>save</span></button>
								<button class="cancel cancel_ot" style="float: right;"><span>Cancel</span></button>
								<button class="clear_ot" style="float: right;"><span>Clear</span></button>
							</td>
						</tr>
						</table>
					</div>
					<div class="popFooter"></div>
				</div>
				<div class="message_lock_confirm">
					<p></p>
					<div class="duplicate_buttons">
						<button onClick="$(\'.message_lock_confirm\').css({display:\'none\'});"><span>Ok</span></button>
					</div>
				</div>
				<input type="hidden" name="userid" value="'.@$_GET['userid'].'" id="userid" />
				<input type="hidden" name="user_type" value="'.@$_SESSION['login_status'].'" id="user_type" />
				<input type="hidden" name="user_session_id" value="'.@$_SESSION['user_id'].'" id="user_session_id" />';
			} else {

				if(isset($_COOKIE["lighthouse_rp_data"])) {
						$savedData = explode('~', urldecode($_COOKIE["lighthouse_rp_data"]));
						$savedRole = $savedData[0];

						$savedDate = date("m-d-Y", mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y'),0));
						/*if($savedData[4] != '' && $savedData[4] != null){
						  // $savedDate = date("m-d-Y",$savedData[4]);
   							$savedDate = date("m-d-Y", mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y'),0));
						} else {
							$savedDate = date("m-d-Y", mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y'),0));
					   }*/

					//	$hiddenChar = $savedData[2];
						$savedCompany = $savedData[3];
						$savedProgram = $savedData[6];
						if($savedData[5] != '' && $savedData[5] != null) {
						   //$endDate = date("m-d-Y",$savedData[5]);
						   $endDate = "";
						} else{
							$endDate = "";
						}	

				  if(!empty($savedData[0]) || !empty($savedData[3]) || !empty($savedData[6]) ) {
					$is_filter_selected = '1';
					$hiddenChar = '';
				  }	else {
					$is_filter_selected = '';	
					$hiddenChar = 'a';
				  }
				  setcookie("lighthouse_rp_data", urlencode($savedData[0] . '~' . $savedData[1] . '~' . $savedData[2] . '~' . $savedData[3] . '~' . ''. '~' . '' . '~' . $savedData[6]), time()+220752000, '/');
				  
				} else {
					$savedRole = '';
					$savedDate = date("m-d-Y", mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y'),0));
					$hiddenChar = 'a';
					$savedCompany = '';
					$savedProgram = '';
					$is_filter_selected = '';
				}

				echo '<!--=========== START: COLUMNS ===========-->
            <div class="rp_report popHeader" style="position: fixed; background-color: rgb(255, 255, 255); opacity: 0.3;filter: alpha(opacity = 30); display: none; margin-right: -300px; height: 1024px; width: 3000px; z-index: 1;"></div>
			<div class="column_main_resource">
						<form method="get" action="" target="_blank" name="excelform" id="excelform" onSubmit="return false;">
						<div class="rp_report">
							<div class="close_rp_report">X</div>
							<div class="rp_report_content">
								<ul>
									<li class="label_name">Select Month</li>
									<li class="label_value"> 
										<select class="month_list" id="month_list">
										<option value="0">select</option>
										'.RpDisplay::getMonthdropdown($intCurrMonth,$year).' 
										</select>
									</li>
								</ul>
								<div class="add">
									<button onClick="generateReport()"><span>Generate</span></button>
								</div>
							</div>
						</div>
						</form>
						<!--==| START: Bucket |==-->
						<div class="main_actions main_actions_resource">
							<button class="monitors" id="week_loading"><span>Week loading: 0%</span></button>

							<button class="monitors" id="quarter_loading"><span>Quarter loading: 0%</span></button>
							<button class="monitors" id="year_loading"><span>Year loading: 0%</span></button>';
							if($_SESSION['login_status'] == "admin"){
								echo '<button onClick="$(\'.rp_report\').css({display:\'block\'}); return false;"><span>run report</span></button>';
							}
						echo '</div>
						<!--==| END: Bucket |==-->

						<!--==| START: Bucket |==-->
						<div class="title_med">
							<div style="float:left;">
								<div style="padding-left:5px;width:300px;" class="top_actions">
									<label>Program &nbsp;</label>
									<select id="selected_program" name="selected_program">
										<option value="">All Programs</option>
										'.RpDisplay::fullProgramListHTML($savedProgram).'
									</select>
								</div>
								<div style="clear:both"></div>
								<div style="width: 300px;float:left;*width:380px;" class="top_actions">	
									<label>Company &nbsp;</label>
									<select class="company_list" style="*float: left;">
										<option value="">All Companies</option>
										'.RpDisplay::fullCompanyListHTML($savedCompany).'
									</select>
									<div class="week_label" style="font-size: 13px; font-weight: bold; padding-top: 10px; width: 376px;"></div>
								</div>
							</div>
							<div style="float:right;width:250px;">
								<div style="width:300px;">
									<label>Displaying</label>
									<select class="resource_types">
										<option value="">All Resource Types</option>
										'.RpDisplay::resourceListHTML($savedRole).'
									</select>
								</div>

							<div class="right_actions datePick" style="width:250px;">								
								<label> From: </label><input type="text" value="--" id="basics" class="jumptodatecal"/>
								<label>&nbsp;To: </label><input type="text" value="--" id="basicsFrom" class="jumptodatecalFrom"/>
							</div>
							</div>
						</div>
						<!--==| END: Bucket |==-->

						<!--==| START: Bucket |==-->
						<div class="resource_jumpto_list">
							<input type="hidden" id="jumptoID" name="jumptoID" value="' . $hiddenChar . '">
							<input type="hidden" id="weekStartDate" name="weekStartDate" value="' . $savedDate . '">
							<ul class="alphalist">
								<li class="jumpto">Jump To</li>
								' .RpDisplay::getAlphaJumpTo($savedProgram,$savedRole, $savedDate, $savedCompany,$endDate) .'
							</ul>
						</div>
				<!--		<div class="resources_controller">
							<ul class="days_container">
								<li class="arrows"><button class="arrows arrows_left"></button></li>

								<li class="first_day 1_col">Mon<br/></li>
								<li class="2_col">Tues<br/></li>
								<li class="3_col">Wed<br/></li>
								<li class="4_col">Thu<br/></li>
								<li class="last_day 5_col">Fri<br/></li>

								<li class="arrows"><button class="arrows arrows_right"></button></li>
							</ul>
						</div> -->
						<!--==| END: Bucket |==-->

						<!--==| START: Bucket |==-->
						<div class="schedules_container" style="overflow:auto;max-height:936px;_max-height:936px;*max-height:936px;height:auto;width:651px;margin-top:0px">
							<div class="schedules_controller">
							' .RpDisplay::fullListInit($savedProgram , $savedRole, $savedDate, $is_filter_selected, $savedCompany,$endDate) .'
							</div>
						</div>
						<!--==| END: Bucket |==-->

					</div>

					<!--=========== COLUMN BREAK ===========-->

					<div class="column_right_resource">
					<!-- allocation_expanded allocation_collapsed -->

						<!--==| START: Bucket |==-->
						<div class="title_med allocation_expanded"><h4>Allocation Type</h4></div>
						<div class="allocation_type">
							<ul>
							<li class="key_deselect">De-select current selection</li>
							<li class="key_overhead" ' . $display_style . '>Overhead / Internal</li>
							<li class="key_outoffice" ' . $display_style . '>Out of Office</li>
							<li class="key_blank">Unassigned</li>
							<li class="key_convertbillable" ' . $display_style . '>Convert hours to Actual</li>
							</ul>
						</div>
						<!--==| END: Bucket |==-->


						<!--==| START: Bucket |==-->
						<div class="title_med">
							<h4>Hours</h4>
							<div class="right_actions right_actions_small">';
						if($_SESSION['login_status'] == "admin"){
							echo '<input name="hours_type" class="hours_type" id="hours_type_scheduled" type="radio" value="scheduled" /><label class="small">Scheduled</label>
								<input name="hours_type" class="hours_type" id="hours_type_actual" type="radio" value="actual" CHECKED /><label class="small">Actual</label>';
						}else{
							echo '<input name="hours_type" class="hours_type" id="hours_type_scheduled" type="radio" value="scheduled" CHECKED /><label class="small">Scheduled</label>';
						}
						echo '</div>
						</div>

						<div class="hours">
							<ul>' . RpDisplay::projectListHTML() .'</ul>
						</div>
						<!--==| END: Bucket |==-->

					</div>

					<!--=========== END: COLUMNS ===========-->

					</div>
				</div>
				<div id="tooltip" class="rp_block_tooltip"></div>
				<div id="blur" class="blur jHelperTipClose"></div>

				<div id="popHours" class="popHours" style="display: none; position: absolute; top: 0px; left: 0px; z-index: 999;">
					<div class="popHeader"></div>
					<div class="popMain">
						<table>
						<tr>
							<td>
								<label>Projects:</label>
								<select name="overtime_project" id="overtime_project" style="width: 290px;">
									<option value="">--Select Project--</option>
									'.RpDisplay::fullProjectListHTML().'
								</select>
							</td>
						</tr>
						<tr>
							<td width="80">
								<label>Hours:</label><br />
								<select name="overtime_hours" id="overtime_hours" style="width: 57px;">
									<option id="hours0" value="0">0</option>
									<option id="hours1" value="1">1</option>
									<option id="hours2" value="2">2</option>
									<option id="hours3" value="3">3</option>
									<option id="hours4" value="4">4</option>
									<option id="hours5" value="5">5</option>
									<option id="hours6" value="6">6</option>
									<option id="hours7" value="7">7</option>
									<option id="hours8" value="8">8</option>
									<option id="hours9" value="9">9</option>
									<option id="hours10" value="10">10</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label>Notes:</label>
								<textarea name="overtime_notes" id="overtime_notes" style="width: 290px; height: 48px"></textarea>
							</td>
						</tr>
						<tr>
							<td>
								<button id="saveBtn" style="float: right;"><span>save</span></button>
								<button class="cancel cancel_ot" style="float: right;"><span>Cancel</span></button>
								<button class="clear_ot" style="float: right;"><span>Clear</span></button>
							</td>
						</tr>
						</table>
					</div>
					<div class="popFooter"></div>
				</div>';
			}
		}
	}

?>
