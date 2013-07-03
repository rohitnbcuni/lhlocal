<?PHP
	include('WorkOrders.inc');
	define('NBCDOTCOM' , 8);
	class Workorders_IndexController extends LighthouseController { 
		public function indexAction() {
		$cnt = 5;
			if($_SESSION['login_status'] == "client") {
				echo '<input type="hidden" name="client_login" id="client_login" value="client" />';
			} else {
				echo '<input type="hidden" name="client_login" id="client_login" value="employee" />';
			}
			$status_active = ($_REQUEST['status'] == '1') ? 'selected' : '';
			$status_archive = ($_REQUEST['status'] == '0') ? 'selected' : '';
			$status_draft = ($_REQUEST['status'] == '-1') ? 'selected' : '';
			echo '<!--=========== START: COLUMNS ===========-->
				<!--==| START: Bucket |==-->
					<div class="message_archive_select_check message_archive message_unarchive message_active" style="width:3000px;height:1024px;position:fixed;background-color:#ffffff;z-index:1;margin-top:-500px;margin-left:-630px;opacity: 0.3; filter: alpha(opacity = 30); zoom:1;"></div>
				<div class="main_actions_wo">
					
					<button onClick="window.location = \'/workorders/index/create/\';"><span>create new workorder</span></button>
					<form name="gotowo_form" onSubmit="javascript:return gotoWorkorder();">
						<input type="text" value="id #" onBlur="javascript:if (this.value == \'\') this.value = \'id #\';" onFocus="javascript:if (this.value == \'id #\') this.value=\'\';" class="field_xsmall" id="wo_id" name="wo_id"/>
						<span class="submit_button_span">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
						<input type="submit" value="go" class="submit_button">
					</form>
					<div id = "pagination" style="position:absolute;margin-left:290px;_margin:3px 0 0 30px;*margin:3px 0 0 30px;width:450px;display:none;"></div>
					<INPUT TYPE="hidden" ID="current_page_set" VALUE="1"/>
					<INPUT TYPE="hidden" ID="current_page" VALUE="1"/>
					<button onClick="return generateWOReport();" style="float:right;"><span>Generate Report</span></button>
					<button id="archiveBTN" onClick="archiveWO_CheckList();" style="float:right;"><span>Archive</span></button>
				</div>
						
				<!--==| END: Bucket |==-->

				<!--==| START: Bucket |==-->

				<div class="title_med workorders_filter">
					<label for="client_filter" id="client_filter_label">Client</label>
					<select id="client_filter" onchange="changeCompany();">
						<option value="-1">Show All</option>
					'.WoDisplay::getCompanyHTML().'
					</select>
					<label for="project_filter" id="project_filter_label">Project</label>
					<select id="project_filter" onchange="displayWorkorders(\'1\',\'first\',\'title\',\'1\',\'2\');">
						<option value="-1">Show All</option>
					</select>
					<label for="status_filter" id="status_filter_label">Status</label>
					<select id="status_filter" onchange="displayWorkorders(\'1\',\'first\',\'title\',\'1\');">
						<option value="-1">Show All</option>
						'.WoDisplay::getAllStatusOptionHTML().'
						<option value="over_due">Over Due</option>
						<option value="99">Open</option>
					</select>
					<label for="assigned_filter" id="assigned_filter_label">Assigned To</label>
					<select id="assigned_filter" onchange="displayWorkorders(\'1\',\'first\',\'title\',\'1\',\'2\');">
						<option value="-1">Show All</option>
					</select>

					<label for="project_status_filter" id="project_status_filter_label">Type</label>
					<select id="project_status_filter" onchange="getWO_On_Status();">
						<option value="1" '.$status_active.'>Active</option>
						<option value="0" '.$status_archive.'>Archived</option>
						<option value="-1" '.$status_draft.'>Draft</option>
					</select>
				</div>';
				$wo_data_cookie = isset($_COOKIE["lighthouse_wo_data"])? $_COOKIE["lighthouse_wo_data"] : "";
				$req_Type_Arr = Array();
				$req_Type_Arr['Outage'] = ' selected="selected"';
				$req_Type_Arr['Problem'] = ' selected="selected"';
				$req_Type_Arr['Request'] = ' selected="selected"';

				if(!empty($wo_data_cookie))
				{

					$wo_data_cookie_all = explode("~", @$wo_data_cookie);
					$requestTypeFilter = $wo_data_cookie_all[4];
					if(!empty($requestTypeFilter))
					{
						$req_Type_Arr = Array();
						$requestTypeFilter_all = explode(",", @$requestTypeFilter);
						for($u = 0; $u < sizeof($requestTypeFilter_all); $u++) {
							if(!empty($requestTypeFilter_all[$u]))
							{
								$req_Type_Arr[$requestTypeFilter_all[$u]] = ' selected="selected"';
							}
						}
					}
				}
				$end_date_default = date("m/d/Y");// current date;
				$start_date_add_one_month = strtotime(date("m/d/Y", strtotime($end_date_default)) . "-1 month");
				$start_date_default = date("m/d/Y", $start_date_add_one_month);
        echo '
				<INPUT TYPE="hidden" ID="start_date_hidden" VALUE="'.$start_date_default.'" />
				<INPUT TYPE="hidden" ID="end_date_hidden" VALUE="'.$end_date_default.'" />
				<INPUT TYPE="hidden" ID="search_hidden" VALUE="" />';

				echo '<div class="title_med2 workorders_filter">
				<INPUT TYPE="hidden" ID="requestTypeFilter" VALUE"" />
					<label for="project_status_filter" style="color: #fff;float: left; margin-top: 10px; font-size: 15px;" id="project_status_filter_label">Request Type</label>
					<select id="control_7" name="control_7[]" multiple="multiple" size="5">
						<option value=""></option>
						<option value="Outage" '.$req_Type_Arr['Outage'].'>Outage</option>
						<option value="Problem" '.$req_Type_Arr['Problem'].'>Problem</option>
						<option value="Request" '.$req_Type_Arr['Request'].'>Request</option>
				</select>';

				echo '<div class="title_med workorders_filter" style="position: inherit;align:center;">
				   <label style="padding-left:15px;" for="requestedby_filter" id="requestedby_filter_label">Request By</label>
					<select id="requestedby_filter" style="width: 140px;" onchange="displayWorkorders(\'1\',\'first\',\'title\',\'1\',\'2\');">
						<option value="-1">Show All</option>
					</select>
					  <div id = "search_and_date_filters" style="display: none;">
					<button style="margin-top:5px;" onClick="$(\'.wo_date_range_filter\').css({display:\'block\'}); return false;"><span>Date Filters</span></button>
					</div>
				</div>
				 	
				 
				 
				<!-- ==| START: view type Botton Divison#7927 |== -->

		         <div style="float: right; margin: 0pt; position: absolute; right: 12px; top: 85px;padding-right: 11px" id="list_view" >
		       		<a href="/workorders/index/calendarview" title="Calendar View" style="padding-right: 6px;" ><img src="/_images/cal_active.png" alt="Calendar View" width="23" height="23" /></a>
						         
					<img src="/_images/list_dis_05.png" alt="List View"  title="List View" width="23" height="23" />

				</div>
		         <!-- ==| END: view type Botton Divison#7927 |== -->
				
        </div>
				<!--==| END: Bucket |==-->
				
				<!--==| START: Sorting |==-->
				<ul class="project_filters workorders_sort">
					<li class="id"><a href="#" class="down" id="idsort" onClick="sortWorkorders(\'id\'); return false;">id</a></li>
					<li class="title"><a href="#" class="down" id="titlesort" onClick="sortWorkorders(\'title\'); return false;">title</a></li>
					<li class="req_type"><a id="req_typesort" href="#" onClick="sortWorkorders(\'req_type\'); return false;">Request Type</a></li>
			

                                        <li class="status"><a id="statussort" href="#" onClick="sortWorkorders(\'status\'); return false;">status</a></li>
					<li class="requested"><a id="requested_bysort" href="#" onClick="sortWorkorders(\'requested_by\'); return false;">requested by</a></li>
					<li class="assigned"><a id="assigned_tosort" href="#" onClick="sortWorkorders(\'assigned_to\'); return false;">assigned to</a></li>
					<li class="opendate"><a id="open_datesort" href="#" onClick="sortWorkorders(\'open_date\'); return false;">open date</a></li>
					<li class="due_date"><a id="due_datesort" href="#" onClick="sortWorkorders(\'due_date\'); return false;">due date</a></li>
					<li class="lastcommentby"><a id="lastcommentbysort" href="#" onClick="sortWorkorders(\'lastcommentby\'); return false;">last commented by</a></li>
					<li class="commentdate"><a id="commentdatesort" href="#" onClick="sortWorkorders(\'commentdate\'); return false;">commented date</a></li>
				</ul>

				<!--==| END: Sorting |==-->

				<!--==| START: Work Orders |==-->';
				$inStyle = '';
				$ua = $_SERVER['HTTP_USER_AGENT'];
				$checker = array(
				  'iphone'=>preg_match('/iPhone|iPod|iPad/', $ua),
				  'blackberry'=>preg_match('/BlackBerry/', $ua),
				  'android'=>preg_match('/Android/', $ua),
				);
			
				if ($checker['iphone'] || $checker['blackberry'] || $android['android']){
				//	  $inlineStyle = "style='height:auto;overflow:hidden'";
				 $inlineStyle = "style='background:none repeat scroll 0 0 #C9C9C9;clear:both;height:auto;overflow:hidden;padding: 0 3px 3px;position: relative;'";
		
						
				}
				echo '<input type="hidden" name="active_wo" id="active_wo" value="" />
				<div id="wo_containter" class="workorders_container" '.$inlineStyle.'>';
					
					echo '<!-- Company Break -->
				</div>
				<!--==| END: Work Orders |==-->
			<!--=========== END: COLUMNS ===========-->
			<div class="message_archive">
				<p>
					You are about to archive this work order. <br /> you want to continue?
				</p>
				<input type="hidden" name="archive_project_confirm" id="archive_project_confirm" value="" />
				
				<div style="clear: both;"></div>
				
				<div class="duplicate_buttons">
					<button onClick="archiveWo(document.getElementById(\'active_wo\').value); return false;"><span>Yes</span></button> 
					<button class="cancel" onClick="$(\'.message_archive\').css({display:\'none\'}); return false;"><span>No</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>
			
			
			
    <div class="message_active">
      <p>
      You are about to active this work order. <br /> you want to continue?
      </p>
        <input type="hidden" name="archive_project_confirm" id="archive_project_confirm" value="" />        
        <div style="clear: both;"></div>      
        <div class="duplicate_buttons">
          <button onClick="activeWo(document.getElementById(\'active_wo\').value); return false;"><span>Yes</span></button> 
          <button class="cancel" onClick="$(\'.message_active\').css({display:\'none\'}); return false;"><span>No</span></button>
          <div style="clear: both;"></div>
        </div>
    </div>
			
		<div class="wo_date_range_filter">
			<div class="close_wo_date_range_filter" onClick="$(\'.wo_date_range_filter\').css({display:\'none\'}); return false;">X</div>
				<div class="wo_date_range_filter_content">
					<div style="display:none">
					<input style = "margin-top:6px" type="text" value = "" id = "search_text"/>
					</div>	
					 <label class="small">Start Date</label>
		  			 <input type="text"   id="start_date_input" value="'.$start_date_default.'" readonly="readonly" />
					 <label  class="small">End Date</label>
					 <input type="text"  id="end_date_input" value="'.$end_date_default.'" readonly="readonly" />
					 <br/>
					 <button style="margin-top:5px;margin-left:371px" onClick="displayWorkorders(\'1\',\'first\',\'title\',\'1\',\'1\');$(\'.wo_date_range_filter\').css({display:\'none\'});" ><span>GO</span></button> 

	            </div>
				
			</div>
		</div>

			<div style="display: none;" id="wo_dimmer_ajax" class="wo_save_box">
				<img alt="ajax-loader" src="/_images/ajax-loader.gif"/>
			</div>

			<div class="message_archive_select_check">
				<p id="message_archive_select_check_para">
					You need to select the work order to Archive/un-archive. <br />
				</p>
				<input type="hidden" name="archive_project_confirm" id="archive_project_confirm" value="" />
				
				<div style="clear: both;"></div>
				
				<div class="duplicate_buttons">
					<button class="cancel" onClick="$(\'.message_archive_select_check\').css({display:\'none\'}); return false;"><span>Cancel</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>		

			<div class="message_unarchive">
				<p>
					You are about to un-archive this work order. <br /> you want to continue?
				</p>
				<input type="hidden" name="unarchive_project_confirm" id="unarchive_project_confirm" value="" />
				
				<div style="clear: both;"></div>
				
				<div class="duplicate_buttons">
					<button onClick="unarchiveWo(document.getElementById(\'active_wo\').value); return false;"><span>Yes</span></button> 
					<button class="cancel" onClick="$(\'.message_unarchive\').css({display:\'none\'}); return false;"><span>No</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>
			<div style="display: none;" id="wo_dimmer_ajax" class="wo_save_box">
				<img alt="ajax-loader" src="/_images/ajax-loader.gif"/>
			</div>';
		}
		public function createAction() {
			if($_SESSION['login_status'] == "client") {
				$hideStyle = "display: none;";
				if($_SESSION['company'] == NBCDOTCOM){
					$readonly = false;
				}else{
					$readonly = true;
				}
			} else {
				$hideStyle = "";
				$readonly = false;
			}
			
			$new_wo_id ="";
			$wo_archive_status = "";
			$wo_archive_text = "";
			$proj_select = "";
			$option_SEVERITY ='_blank';
			$option_INFRA_TYPE ='_blank';
			$closed_wo_style = '';
			if((isset($_REQUEST['wo_id']) && !empty($_REQUEST['wo_id'])) || isset($_REQUEST['copyWO']) ) {
				 $wo_id = ($_REQUEST['wo_id']);
				/* LH fixes
				 * LH#21355
				 */
				/*if(!is_numeric($wo_id )){
					$this->_redirect("workorders/index/");
				}*/
				if( isset($_REQUEST['copyWO']))
				{
					$new_wo_id = $_REQUEST['copyWO'];
				}else
				{
					$new_wo_id = ($_REQUEST['wo_id']);
				}
				/* LH fixes
				 * LH#21355
				 */
				if(!is_numeric($new_wo_id )){
					$this->_redirect("workorders/index/");
				}
				$wo_data = WoDisplay::getQuery("SELECT * FROM `workorders` WHERE `id`='$new_wo_id' LIMIT 1");
				/* LH fixes
				 * LH#21355
				 */
				if(count($wo_data) == 0){
					$this->_redirect("workorders/index/");
				}
      
				if($wo_data[0]['active'] == 0) {
					$draft_style = "block";
					$checked = "checked";
				} else {
					$checked = "";
					$draft_style = "none";
					$draft_check_box_sytle = "none";
				}
				if(($wo_data[0]['status'] == '1') && !isset($_REQUEST['copyWO'])){
					$closed_wo_style='style="background-color:#ffffff;" disabled="disabled"';
				}

				if( isset($_REQUEST['copyWO']) && $wo_data[0]['active'] != 0){
                  $checked = "";
				  $draft_check_box_sytle = "block";
				}

				$wo_custom_data = WoDisplay::getQuery("SELECT * FROM `workorder_custom_fields` WHERE `workorder_id`='$new_wo_id'");
				
				
				$custom_feild_arr;
				foreach($wo_custom_data as $row )
				{
					$custom_feild_arr[$row['field_key']] = $row['field_id'];
				}

				$workorder_audit = WoDisplay::getQuery("SELECT wa.*,at.name  FROM `workorder_audit` wa,`lnk_audit_trial_types` at where workorder_id = '$wo_id' and at.id = wa.audit_id order by `log_date`");

				if($custom_feild_arr['REQ_TYPE']=='1')
				{
					$li_SEVERITY = 'style="display:none;"';
					$li_REQ_DATE = 'style="display:none;"';
					$li_INFRA_TYPE = 'style="display:none;"';
					$li_CRITICAL = 'style="display:none;"';
					$li_DRAFT = 'style="display:none;"';
					$fade_REQ_DATE = 'style="display:block;"';
					$option_SEVERITY ='disable';
					$option_INFRA_TYPE ='disable';
				}
				else if($custom_feild_arr['REQ_TYPE']=='2')
				{
					$li_SEVERITY = 'style="display:block;"';
					$li_REQ_DATE = 'style="display:block;"';
					$li_INFRA_TYPE = 'style="display:none;"';
					$li_CRITICAL = 'style="display:none;"';
					$li_DRAFT = 'style="display:none;"';
					$fade_REQ_DATE = 'style="display:block;"';

					$option_SEVERITY ='_blank';
					$option_INFRA_TYPE ='disable';
				}
				else if($custom_feild_arr['REQ_TYPE']=='3')
				{
					$li_SEVERITY = 'style="display:none;"';
					$li_REQ_DATE = 'style="display:block;"';
					$li_INFRA_TYPE = 'style="display:block;"';
					$li_CRITICAL = 'style="display:block;"';
					$li_DRAFT = 'style="display:block;"';
					$fade_REQ_DATE = 'style="display:none;"';
					$option_SEVERITY ='disable';
					$option_INFRA_TYPE ='_blank';
				}
				if(!isset($_REQUEST['copyWO']))
				{
					$requester_id = $wo_data[0]['requested_by'];
				}else
				{
					$requester_id = $_SESSION['user_id'];
				}
				$requestors_data = WoDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$requester_id ."' LIMIT 1");

				if(!isset($_REQUEST['copyWO'])){
					if($wo_data[0]['archived'] == '1'){
						$wo_archive_status = " wo_archived ";
						$wo_archive_text = " readonly ";
					}
				}
                
				$launch_date_time_part = explode(" ", @$wo_data[0]['launch_date']);
				$launch_date_part = explode("-", @$launch_date_time_part[0]);
				$launch_time_part = explode(":", @$launch_date_time_part[1]);
				
				$start_date_time_part = explode(" ", @$wo_data[0]['creation_date']);
				$start_date_part = explode("-", @$start_date_time_part[0]);
				$start_time_part = explode(":", @$start_date_time_part[1]);
				
				$completed_date_time_part = explode(" ", @$wo_data[0]['completed_date']);
				$completed_date_part = explode("-", @$completed_date_time_part[0]);
				$completed_time_part = explode(":", @$completed_date_time_part[1]);
				
				$closed_date_time_part = explode(" ", @$wo_data[0]['closed_date']);
				$closed_date_part = explode("-", @$closed_date_time_part[0]);
				$closed_time_part = explode(":", @$closed_date_time_part[1]);
				
                $estemated_date_time_part = explode(" ", @$wo_data[0]['estimated_date']);
				$estimated_date_part = explode("-", @$estemated_date_time_part[0]);
				$estimated_time_part = explode(":", @$estemated_date_time_part[1]);
		



				$draft_date_time_part = explode(" ", @$wo_data[0]['draft_date']);
				$draft_date_part = explode("-", @$draft_date_time_part[0]);
				$draft_time_part = explode(":", @$draft_date_time_part[1]);
			
				$pageLoadHide = 'style="display:block;"';
			} else {
				$wo_id = "";
				$proj_select = isset($_COOKIE["lighthouse_create_wo_data"])? $_COOKIE["lighthouse_create_wo_data"] : "";
				$pageLoadHide = 'style="display:none;"';
				$li_INFRA_TYPE = 'style="display:none;"';
				$li_CRITICAL = 'style="display:none;"';
				$li_DRAFT = 'style="display:none;"';
				$checked = "false";
				$draft_style = "none";
			}
			$launch_date_min = substr($wo_data[0]['launch_date'],14,2);
	if ($launch_date_min==''){$launch_date_min='00';}
			echo '  <input type="hidden" name="currentMinute" id="currentMinute" value="'.$launch_date_min.'"> ';
			echo '<input type="hidden" id="prompt_save" value="1"><body onbeforeunload="if($(\'#prompt_save\').val() == 2){return \'You have not saved this workorder! If you leave this form all information you have entered will be lost. Are you sure you want to leave this page without saving?\';}"><div class="column_full_workorders">

				<!--==| START: Bucket |==-->
				<div class="title_med workorders_filter">
					<div class="title_actions"><button class="back_arrow" onClick="window.location= \'/workorders/\';"><span>all work orders</span></button></div>
					<h4>Work Order Information</h4>';
					if(isset($_REQUEST['wo_id'])) {

						echo '<div style="float:right;margin:6px 7px 0 -5px;"><button onClick="copyRequest(\''.$_REQUEST['wo_id'].'\');"><span>Copy Request</span></button></div>';
					}
					
				echo'</div>
				<div class="content">
					<div class="wo_dimmer" id="wo_dimmer" style="display:none;"></div>
					<div class="wo_save_box" id="wo_dimmer_ajax" style="display: none;"><img src="/_images/ajax-loader.gif" alt="ajax-loader"/></div>
					<!--<div class="wo_dimmer" id="wo_dimmer"></div>
					<div class="wo_save_box" id="wo_dimmer_ajax"><img src="/_images/ajax-loader.gif" alt="ajax-loader"/></div>-->
					<!--=========== COLUMN BREAK ===========-->
					<div class="workorder_content_col1">';
						// "LH#23699Security Risk: Sensitive Information ...";
						//echo '<input type="hidden" name="user_id" id="user_id" value="' .$_SESSION['user_id'] .'" />
						//<input type="hidden" id="assignedToUserIdHidden" value="' .$wo_data[0]['assigned_to'] .'" />';
						$requested_by_prev = ($wo_data[0]['requested_by'] != '')?$wo_data[0]['requested_by']:'';
						echo '<input type="hidden" name="woRequestedByPrev" id="woRequestedByPrev" value="'.$requested_by_prev.'" />';	
						echo'<input type="hidden" id="woStatusIdHidden" value="' .$wo_data[0]['status'] .'" />
						<ul>
							<li>
								<label for="wo_requested_by" id="wo_requested_by_label">Requested By:</label>
								<div id="requestor_loader" style="margin-left:186px"><img  src="/_images/loading.gif" alt="loading.." /></div>
								<div id="requestor_loader_field" style="display:none;">';
								echo '<select "'.$closed_wo_style.'" class="field_medium" name="wo_requested_by" id="wo_requested_by" onchange="getRequestorsInfo(this.value);">
								</select>
								</div>
								</li>
							<li>
								<label for="wo_request_type" id="wo_request_type_label">I\'d Like To:</label>';
									if(isset($_REQUEST['wo_id'])) {
											
								echo '<select "'.$closed_wo_style.'" class="field_medium" name="REQ_TYPE" id="REQ_TYPE"  onchange = "getRequestType(this.value);" >
									<option value="_blank"></option>
								';
									}else{
										echo '<select "'.$closed_wo_style.'" class="field_medium" name="REQ_TYPE" id="REQ_TYPE"  onchange = "getRequestTypeNew(this.value);" >
									<option value="_blank"></option>
								';
									}	
							
								echo WoDisplay::getcustomDropDown("REQ_TYPE",$custom_feild_arr['REQ_TYPE']);
								echo '</select>
							</li>';
							if(isset($_REQUEST['wo_id'])) {
									if($wo_data[0]['launch_date'] != "") {
										$fade_time_sens = ' style="display: none;"';
										$launch_date = @date("m/d/Y", mktime(0,0,0,$launch_date_part[1],$launch_date_part[2],$launch_date_part[0]));

										
										$launch_time = @date("h:i ", mktime($launch_time_part[0],$launch_time_part[1],$launch_time_part[2],0,0,0));
										$launch_time_new = @date("h:i A", mktime($launch_time_part[0],$launch_time_part[1],$launch_time_part[2],0,0,0));

										if(@date("a", mktime($launch_time_part[0],$launch_time_part[1],$launch_time_part[2],0,0,0)) == "am") {
											$launch_am = ' SELECTED ';
											$launch_pm = '';
										} else if(@date("a", mktime($launch_time_part[0],$launch_time_part[1],$launch_time_part[2],0,0,0)) == "pm") {
											$launch_am = '';
											$launch_pm = ' SELECTED ';
										} else {	
											$launch_am = '';
											$launch_pm = '';
										}
									}
									else {
										$fade_time_sens = '';
										$launch_date = '';
										$launch_time = 'hh:mm';
										$launch_am = '';
										$launch_pm = '';
									}							
									if($wo_data[0]['draft_date'] != "") {
										$fade_time_sens = ' style="display: none;"';
										if(checkdate($draft_date_part[1], $draft_date_part[2], $draft_date_part[0])){
											$draft_date = @date("m/d/Y", mktime(0, 0, 0, $draft_date_part[1], $draft_date_part[2], $draft_date_part[0]));
										} else {
											$draft_date = '';
										}
										
										$draft_time = @date("h:i", mktime($draft_time_part[0],$draft_time_part[1],$draft_time_part[2],0,0,0));
										$draft_time_new = @date("h:i A", mktime($draft_time_part[0],$draft_time_part[1],$draft_time_part[2],0,0,0));
										
										if(@date("a", mktime($draft_time_part[0],$draft_time_part[1],$draft_time_part[2],0,0,0)) == "am") {
											$draft_am = ' SELECTED ';
											$draft_pm = '';
										} else if(@date("a", mktime($draft_time_part[0],$draft_time_part[1],$draft_time_part[2],0,0,0)) == "pm") {
											$draft_am = '';
											$draft_pm = ' SELECTED ';
										} else {	
											$draft_am = '';
											$draft_pm = '';
										}
									}
									else {
										$fade_time_sens = '';
										$draft_date = '';
										$draft_time = 'hh:mm';
										$draft_am = '';
										$draft_pm = '';
									}					
								}
								else {
									$fade_time_sens = '';
									$launch_date = '';
									$launch_time = 'hh:mm';
									$launch_am = '';
									$launch_pm = '';
									$draft_date = '';
									$draft_time = 'hh:mm';
									$draft_am = '';
									$draft_pm = '';
								}
						echo'</ul><div '.$pageLoadHide.' id="pageLoadHide"> <ul>
							<li>
								<label for="wo_project" id="wo_project_label">Project:</label>';
								$project_by_prev = ($wo_data[0]['project_id'] != '')?$wo_data[0]['project_id']:'';
								echo '<input type="hidden" name="hidden_projecd_id" id="hidden_projecd_id" value="'.$project_by_prev.'">
								<div id="project_loader" style="margin-left:186px"><img  src="/_images/loading.gif" alt="loading.." /></div>
								<div id="project_loader_field" style="display:none;">
								<select "'.$closed_wo_style.'" class="field_medium" name="wo_project" id="wo_project">';
									
								echo '</select>
								</div>
							</li>
							<li id="li_CRITICAL" '.$li_CRITICAL.' >
								<label for="wo_critical" id="wo_critical_label">Critical:</label>
								<input "'.$closed_wo_style.'" type="checkbox" name="CRITICAL" value="TRUE" id="CRITICAL"  '.WoDisplay::getcustomCheckbox("CRITICAL",$custom_feild_arr['CRITICAL']).' >';
								echo '								
							</li>
							<li id="li_SEVERITY" '.$li_SEVERITY.'>
								<label for="wo_severity" id="wo_severity_label">Severity:</label>';
								 if(isset($_REQUEST['wo_id'])) 
								 {
								 
								 echo '<input type="hidden" name="woesd" id="woesd" value="'.$_REQUEST['wo_id'].'" >';
								 echo '<select "'.$closed_wo_style.'" class="field_medium" name="SEVERITY" id="SEVERITY" onchange="severityChange(this.value)">';}
								 else
								 {echo '<select "'.$closed_wo_style.'" class="field_medium" name="SEVERITY" id="SEVERITY" onchange="severityChange(this.value)">';
								}
								echo '<option value="'.$option_SEVERITY.'"></option>
								';
								echo WoDisplay::getcustomDropDown("SEVERITY",$custom_feild_arr['SEVERITY']);
								echo '</select>
								<input type="hidden" name="currentDateTime" id="currentDateTime" value="'.date("Y-m-d:H-i").'">
							</li>
							<li id="li_REQ_DATE" '.$li_REQ_DATE.'>
								<div "'.$closed_wo_style.'" class="wo_time_fade" id="wo_time_fade" '.$fade_REQ_DATE.'></div> 
								<div>
									<label for="time_sensitive_date">Required Date:</label><span cal_select class="datePick">';
							       if(isset($_REQUEST['wo_id'])) {
									echo '<input "'.$closed_wo_style.'" name="time_sensitive_date" id="time_sensitive_date" readonly value="' .$launch_date .'" id="basics" class="date_picker field_small " type="text" /></span>';
									}else {
									echo '<input "'.$closed_wo_style.'" name="time_sensitive_date" id="time_sensitive_date" readonly value="' .$launch_date .'" id="basics" class="date_picker field_small " type="text" onchange="updateEstimatedDate();" /></span>';
									}
								//	echo p($launch_date_time_part);
								echo '<label for="time_sensitive_time" class="inside_label">Required Time:</label>';
								if(isset($_REQUEST['wo_id'])) {
									echo '<select "'.$closed_wo_style.'" name="time_sensitive_time" id="time_sensitive_time" class="small">';
									}else{
									echo '<select "'.$closed_wo_style.'" name="time_sensitive_time" id="time_sensitive_time" class="small" onchange="updateEstimatedDate();" >';
									}
									
									if(isset($_REQUEST['wo_id'])) {
										echo WoDisplay::getDailyHours($launch_time_new);
									} else {

										echo WoDisplay::getDailyHours();
									}
									echo '</select><div style="display:none;">
									<label  for="ampm" class="inside_label_small">AM/PM:</label>';
									if(isset($_REQUEST['wo_id'])) {
									
									echo '<select "'.$closed_wo_style.'" name="ampm" id="ampm" class="small" style="">';
									
									}
									else 
									{
									echo '<select "'.$closed_wo_style.'" name="ampm" id="ampm" class="small" onchange="updateEstimatedDate();" style="">';
									}
								
									echo '<option value="" selected="selected"> -- </option>
										<option value="am"' .$launch_am .'>AM</option>
										<option value="pm"' .$launch_pm .'>PM</option>
										
									</select></div>									
								</div>
							</li>
							<li id="li_DRAFT" '.$li_DRAFT.' >
								<div style="display:'.$draft_check_box_sytle.'">
									<label for="wo_draft" id="wo_draft_label">Future Start Date:</label>
									<input type="checkbox" name="WO_DRAFT" '.$checked.' id="WO_DRAFT" onclick="showDraftTimeField();">
									<div class="wo_draft_time" id="wo_draft_time" style="padding-top:2px; margin-left:124px; clear:both; display:'.$draft_style.'">
										<div style="float:left;">
											<input name="draft_date" id="draft_date" readonly value="'.$draft_date.'" id="basics_draft" class="date_picker field_small" type="text"/>
										</div>
										<label for="time_sensitive_time_draft" class="inside_label" style="width:60px;">Start Time:</label>
										<select name="time_sensitive_time_draft" id="time_sensitive_time_draft" class="small" >';
										if(isset($_REQUEST['wo_id'])) {
											echo WoDisplay::getDailyHours($draft_time_new);
										} else {
											echo WoDisplay::getDailyHours();
										}
										echo '</select><div style="display:none;">
										<label for="ampm_draft" class="inside_label_small">AM/PM:</label>
										<select  name="ampm_draft" id="ampm_draft" class="small" onchange="" style="">
											<option value="0" selected="selected"> -- </option>
											<option value="am"' .$draft_am .'>AM</option>
											<option value="pm"' .$draft_pm .'>PM</option>
										</select></div> 		         
									</div>
								</div>
							</li>
							<li>
								<label for="wo_site_name" id="wo_site_name_label">Please Choose Your Site:</label>
								<select "'.$closed_wo_style.'" class="field_medium" name="SITE_NAME" id="SITE_NAME">
									<option value="_blank"></option>	
								';
								echo WoDisplay::getcustomDropDown("SITE_NAME",$custom_feild_arr['SITE_NAME']);
								echo '</select>
							</li>

							<li><label for="wo_title" id="wo_title_label">Brief Description:</label><input "'.$closed_wo_style.'" name="wo_title" id="wo_title" class="field_large" type="text" value="' . htmlspecialchars(@$wo_data[0]['title']) .'" /></li>
							<li><label for="wo_example_url" id="wo_example_url_label">Example URL:</label><input "'.$closed_wo_style.'" name="wo_example_url" id="wo_example_url" class="field_large" type="text" value="' . htmlspecialchars(@$wo_data[0]['example_url']) .'"/></li>
<!--=========== Ticket# 16857  wrap description body by html_entity_decode ===========-->						
	<li><label for="wo_desc" id="wo_desc_label">Description:</label><textarea "'.$closed_wo_style.'" name="wo_desc" id="wo_desc" class="field_large">' .htmlentities(@$wo_data[0]['body'],ENT_NOQUOTES,'UTF-8').'</textarea></li>
							<li>
								<div class="no_label_side">
									<div class="wo_dimmer" id="file_upload_dimmer" style="display: none;"></div>
									<form id="file_upload_form" name="file_upload_form" accept-charset="utf-8" method="post" action="" enctype="multipart/form-data">
									<input type="hidden" name="workorder_id" id="workorder_id" value="' .@$wo_id .'" />
									<input type="hidden" name="copyWO" id="copyWO" value="' .@$_REQUEST['copyWO'] .'" />
									<input type="hidden" name="dirName" id="dirName" value="' .@$wo_id .'" />
									
									<label for="upload_file" id="upload_file_label">Attach files (each file should be under 10MB)</label>
									<ul class="attached" id="file_upload_list">';
									
										if(isset($_REQUEST['wo_id'])) {
											$file_data = WoDisplay::getQuery("SELECT * FROM `workorder_files` WHERE `workorder_id`='" .$wo_data[0]['id'] ."'");
											
											for($fx = 0; $fx < sizeof($file_data); $fx++) {
												echo '<li id="file_' .$file_data[$fx]['id'] .'">'
													.$file_data[$fx]['file_name']
													.'&nbsp;
													<a href="" onclick="removeFile(\'' .$file_data[$fx]['id'] .'\'); return false;">remove</a>
												</li>';
											}
										}
										
										echo '</ul>
									<div class="uploader">
										<!--<input class="field_medium" type="text"><button class="secondary" /><span>browse</span></button>-->
										<input "'.$closed_wo_style.'" class="field_xlarge" onChange="checkDirName(file_upload_form);" type="file" name="upload_file" id="upload_file" />
										<img src="/_images/ajax-loader.gif" alt="ajax-loader" id="file_upload_ajax" style="display: none;">
									</div>
									<iframe name="upload_target" src="/_ajaxphp/uploadFile.php" width="400" height="100" style="display:none;"> </iframe>
									
								</div>
							</li>
						</ul>
						</div>
					</div>
					<!--=========== COLUMN BREAK ===========-->
					<div class="workorder_content_col2">
						<div class="side_bucket_container" style="' .$hideStyle .'">

							<div class="side_bucket_title">Requestors Information</div>
							<div class="side_bucket_content">
								<img src="'.@$requestors_data[0]['user_img'].'" id="requestor_photo" class="requestor_photo">
								<ul class="requestor_fields">
									<li><label for="requestor_name">Requested By:</label><input class="field_small readonly" name="requestor_name" id="requestor_name" type="text" value="' .@$requestors_data[0]['first_name'] ." " .@$requestors_data[0]['last_name'] .'" readonly /></li>
									<li><label for="requestor_email">E-mail:</label><input class="field_small readonly" name="requestor_email" id="requestor_email" type="text" value="' .@$requestors_data[0]['email'] .'" readonly /></li>
									<li><label for="requestor_phone">Phone:</label><input class="field_small readonly" name="requestor_phone" id="requestor_phone" type="text" value="' .@$requestors_data[0]['phone_office'] .'" readonly /></li>

								</ul>
								<div class="clearer"></div>
							</div>
						</div>
						<div class="side_bucket_container bucket_container_last">
							<div class="side_bucket_title">Project Management</div>
							<div class="side_bucket_content">'
								.WoDisplay::assignDateHTML(@$wo_data[0]['id'])
								.'<ul class="project_management">

									<li>
										<label for="wo_status">Work Order Status:</label>';
										if($_SESSION['login_status'] != "client" || isset($_REQUEST['wo_id'])) {
											echo '<select "'.$closed_wo_style.'" class="field_small" name="wo_status" id="wo_status" onchange="changeAssignedToUser();return false;">';
												if(isset($_REQUEST['wo_id'])) {
													echo WoDisplay::getStatusOptionEditHTML($wo_data[0]['status'],  $readonly, $wo_data[0]['project_id'], $wo_data[0]['active'],$wo_data[0]['archived']);
												} else {
													echo WoDisplay::getStatusOptionHTML($readonly);
												}
												
											echo '</select>';
										} else {
											echo '<input type="hidden" name="wo_status" id="wo_status" value="" />
											<input type="text" class="readonly" readonly value="NEW" />';
										}
									echo '</li>';
									if($_SESSION['login_status'] != "client" || isset($_REQUEST['wo_id']) || ($_SESSION['company'] == NBCDOTCOM)) {
											echo '<li style="display:block;">';
									}
									else
									{
											echo '<li style="display:none;">';
									}

										echo '
											<label for="wo_assigned_user">Assigned To:</label>

											<select "'.$closed_wo_style.'" class="field_small" name="wo_assigned_user" id="wo_assigned_user" >';
												if(isset($_REQUEST['wo_id'])) {
													echo WoDisplay::getUserAssignOptionEditHTML($wo_data[0]['assigned_to'], $readonly);
												} else {
													echo WoDisplay::getUserAssignOptionHTML($readonly);
												}
											echo '</select>
										</li>';  

										echo '<li id="li_INFRA_TYPE" '.$li_INFRA_TYPE.'>
											<label for="wo_infra_type" id="wo_infra_type_label">Infrastructure Request Type:</label>
											<select "'.$closed_wo_style.'" class="field_small" name="INFRA_TYPE" id="INFRA_TYPE" >
												<option value="'.$option_INFRA_TYPE.'"></option>
											';
											echo WoDisplay::getcustomDropDown("INFRA_TYPE",$custom_feild_arr['INFRA_TYPE']);
											echo '</select>
										</li>';



									if($_SESSION['login_status'] != "client" || isset($_REQUEST['wo_id'])) {
										$rally_defect = "";
										$rally_enhancement = "";
										if($wo_data[0]['rally_type'] == "defect"){
											$rally_defect = " CHECKED ";
										}else if($wo_data[0]['rally_type'] == "enhancement"){
											$rally_enhancement = " CHECKED ";
										}
										echo '<input type="hidden" id="wo_rally_flag" name="wo_rally_flag" value="' . $wo_data[0]['rally_flag'] . '">';
										echo '<li class="rally">
											<label for="wo_rally_type" id="wo_rally_type_label">Type:</label>
											<div class="wo_rally_option">
												<p><input class="radio_option" type="radio" name="wo_rally_type" id="wo_rally_defect" value="defect" ' . $rally_defect . ' ><span>Defect</span></p>
												<p><input class="radio_option" type="radio" name="wo_rally_type" id="wo_rally_enhancement" value="enhancement" ' . $rally_enhancement . '><span>Enhancement</span></p>
											</div>
										</li>';

									} else {
										echo '<input type="hidden" name="wo_assigned_user" id="wo_assigned_user" value="" />';
									}
									/*print("<pre>");
									echo ($wo_data[0]['completed_date']);
									print("<pre>");*/
									if(isset($_REQUEST['wo_id'])) {
										if(@$wo_data[0]['creation_date'] != "") {
											$start_date = @date("m/d/Y h:i A", mktime($start_time_part[0],$start_time_part[1],$start_time_part[2],$start_date_part[1],$start_date_part[2],$start_date_part[0]));
										} else {
											$start_date = '';
										}
										if(@$wo_data[0]['completed_date'] != "") {
											$esti_date = @date("m/d/Y h:i A", mktime($completed_time_part[0],$completed_time_part[1],$completed_time_part[2],$completed_date_part[1],$completed_date_part[2],$completed_date_part[0]));
										} else {
											$esti_date = '';
										}
										if(@$wo_data[0]['closed_date'] != "") {
											$close_date = @date("m/d/Y h:i A", mktime($closed_time_part[0],$closed_time_part[1],$closed_time_part[2],$closed_date_part[1],$closed_date_part[2],$closed_date_part[0]));
										} else {
											$close_date = '';
										}
										if(@$wo_data[0]['estimated_date'] != "") {
											$estimated_date = @date("m/d/Y h:i A", mktime($estimated_time_part[0],$estimated_time_part[1],$estimated_time_part[2],$estimated_date_part[1],$estimated_date_part[2],$estimated_date_part[0]));
										} else {
											$estimated_date = '';
										}
									} else {
										$start_date = '';
										$esti_date = '';
										$close_date = '';
                                        $estimated_date = '';

									}
									$launch_time_hr = (int)date('H', strtotime($wo_data[0]['launch_date']));
									if( $launch_time_hr > 0 && $launch_time_hr <12){
										$am_pm_string = 'AM';
									} else {
										$am_pm_string = 'PM';
									}
									if(isset($_REQUEST['wo_id']) && !empty($_REQUEST['wo_id'])){
										$launch_date_time = date('m/d/Y h:i A', strtotime($wo_data[0]['launch_date'])); 
									} else {
										$launch_date_time = '';
									}
									if(isset($_REQUEST['wo_id']) && !empty($_REQUEST['wo_id'])){
    									if($wo_data[0]['estimated_date']==null)
    										{
    											$estimated_date=$launch_date_time;
    										}else{
    											$estimated_date =  date('m/d/Y h:i A', strtotime($wo_data[0]['estimated_date']));
                                            }

											//$estimated_date =  date('m/d/Y h:i', strtotime($wo_data[0]['estimated_date']))." ".$am_pm_string;
										} else {
											$estimated_date = '';
										}
									if (isset($_REQUEST['wo_id'])){
										$create_wo_date_display = "style='display:block;'";
									}else{
										$create_wo_date_display = "style='display:none;'";
									}	
									echo '<li '.$create_wo_date_display.'><label for="start_date">Opened Date:</label><input name="start_date" id="start_date" readonly="readonly" class="readonly" type="text" value="' .$start_date .'"></li>';
									if (isset($_REQUEST['wo_id'])){
									echo '<li><label for="estimated_completion_date">Estimated Completion Date:</label><input name="estimated_completion_date" id="estimated_completion_date" value="'.$estimated_date.'" readonly="readonly" class="readonly" type="text"></li>';
									}
									else {
									echo '<li><label for="estimated_completion_date">Estimated Completion Date:</label><input name="estimated_completion_date" id="estimated_completion_date" value="'.$launch_date_time.'" readonly="readonly" class="readonly" type="text"></li>';
							          }
									echo '<li '.$create_wo_date_display.'><label for="close_date">Close Date:</label><input name="close_date" id="close_date" value="' .$close_date .'" readonly="readonly" class="readonly" type="text"></li>
								</ul>
								<div class="clearer"></div>
							</div>
						</div>
					</div>
					<!--=========== COLUMN BREAK ===========-->
					<div class="workorder_content_actions">';
					if(!empty($_REQUEST['wo_id']) && $wo_data[0]['archived']=='1'){
						echo '<button onClick="$(\'.message_reopen\').css({display:\'block\'}); return false;"><span>Reopen ticket</span></button>';
						echo ' <button onClick="$(\'.message_workorder_audit\').css({display:\'block\'}); return false;"><span>Work order Audit</span></button>';
					} else {
						if(!isset($_REQUEST['copyWO'])) {
							if($wo_data[0]['status']!='1')
							{
								echo '<div class="wo_dimmer" id="save_buttons_dimmer"></div>
								<button onClick="$(\'.message_cancel\').css({display:\'block\'}); return false;"><span>cancel</span></button>';
								if(!empty($_REQUEST['wo_id']))
									echo '<button onClick="saveWorkOrder(); return false;"><span>save</span></button>';
								else
									echo '<button id="wo_save" onClick="saveWorkOrder(\'submit\'); return false;"><span>Submit</span></button>';
							}
							if($wo_data[0]['status']=='1')
							{
								echo '<button onClick="$(\'.message_reopen\').css({display:\'block\'}); return false;"><span>Reopen ticket</span></button>';
							}
							else
							{
								if($wo_data[0]['status']!='6' && $wo_data[0]['status']!='7' && $wo_data[0]['status']!='' ){
									$closeButtonStyle = ' style="display:block;" ';
								}else{
									$closeButtonStyle = ' style="display:none;" ';
								}
								echo ' <button id="closeLHTicketId" ' . $closeButtonStyle. ' onClick="$(\'.message_close\').css({display:\'block\'}); return false;"><span>close ticket</span></button>';
							}
							if(!empty($_REQUEST['wo_id'])){
								echo ' <button onClick="$(\'.message_workorder_audit\').css({display:\'block\'}); return false;"><span>Work order Audit</span></button>';
							}
						}
						else
						{
								echo '<div class="wo_dimmer" id="save_buttons_dimmer"></div>
									<button onClick="$(\'.message_cancel\').css({display:\'block\'}); return false;"><span>cancel</span></button>';
								echo '<button id="wo_save" onClick="saveWorkOrder;(\'submit\'); return false;"><span>Submit</span></button>';
								if($wo_data[0]['status']!='6' && $wo_data[0]['status']!='7' && $wo_data[0]['status']!='' ){
									$closeButtonStyle = ' style="display:block;" ';
								}else{
									$closeButtonStyle = ' style="display:none;" ';
								}
								echo ' <button id="closeLHTicketId" ' . $closeButtonStyle. ' onClick="$(\'.message_close\').css({display:\'block\'}); return false;"><span>close ticket</span></button>';
						}
					}						
						echo '</div>

					<!--=========== COLUMN BREAK ===========-->
				</div>
				<div class="clearer"></div>
				<!--==| END: Bucket |==-->
			
				<!--==| START: Bucket |==-->
				<div class="content">';
					if(!isset($_REQUEST['wo_id'])) {
						echo '<div class="wo_dimmer" id="comment_dimmer"></div>';
					}
					
					$project_data = WoDisplay::getQuery("SELECT * FROM `projects` WHERE `id`='" .@$wo_data[0]['project_id'] ."' LIMIT 1");
					
					echo '<!--=========== COLUMN BREAK ===========-->
					<div class="workorder_content_col3">
						<div class="main_bucket_container">
							<div class="main_bucket_title"><h4 id="number_of_comments">Comments (0)</h4></div>
							<div class="main_bucket_content">
							<ul class="comment_field_container">
									<li><label for="comment">New Comment:</label><textarea name="comment" id="comment" class="field_large ' . $wo_archive_text . '" ' . $wo_archive_text . '></textarea></li>
								</ul><div id="new_comment_notification" style="display:none;">
										<div id="sticky">
											<a class="ui-notify-close ui-notify-cross" href="#">x</a>
											<h1>#{title}</h1>
											<p>#{text}</p>
										</div>
									</div>
								<div class="new_comment_actions ' . $wo_archive_status . '"><button class="secondary" onClick="submitComment(); return false;"><span>Submit Comment</span></button></div>
								<ul class="comments" id="comments_list">';
									
									if(isset($_REQUEST['wo_id'])) {
										$comment_id =0;
										$counts = 1;
										$comment_id_row = array();
										$largest_comment_id =array();
										$comment_data = WoDisplay::getQuery("SELECT * FROM `workorder_comments` WHERE `workorder_id`='" .$wo_data[0]['id'] ."' AND active ='1' AND deleted ='0' order by date Desc");
										$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
										for($cx = 0; $cx < sizeof($comment_data); $cx++) {
											$text=$comment_data[$cx]["comment"];
											//$text_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($text));
											/**
											 * Ticket No 16857,19352
											 * Special Character display 
											 * @var test Comment type
											 */
											 //$text_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",html_entity_decode($text,ENT_QUOTES,'ISO-8859-1'));
											$text_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($text,ENT_NOQUOTES, 'UTF-8'));
											//End ticket
											
											$text_string = nl2br($text_string);
											$comment_date_time_part = explode(" ", $comment_data[$cx]['date']);
											$comment_date_part = explode("-", $comment_date_time_part[0]);
											$comment_time_part = explode(":", $comment_date_time_part[1]);											
											$comment_user_data = WoDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$comment_data[$cx]['user_id'] ."' LIMIT 1");
											$comment_id = $comment_data[$cx]["id"]; 
											$comment_id_row[] = $comment_id;
											$comment_delete = '';
											$comment_update_box = '';
											$date_diff = $this->dateDiffComment($comment_data[$cx]['date']);
											
											if($date_diff['years'] == 0 && $date_diff['days'] == 0 && $date_diff['months'] == 0 && $date_diff['hours'] == 0 && $date_diff['minuts'] <= 15){
												//echo "ss". $date_diff['years']." ".$date_diff['days']." ".$date_diff['months']." ".$date_diff['minuts'];
												if($comment_data[$cx]['user_id'] == $_SESSION['user_id']){
													if($counts == 1){
														$comment_delete = "<div id='edit_pannel_".$comment_id."'> <span id='comment_edit' style='padding-left:10px;' onclick='displayCommentBox(".$comment_id.");'><img src='/_images/b_edit.png' alt='Edit' title='Edit'></span><span style='padding-left:10px;' id='comment_delete' onclick='deleteComment(".$comment_id.");' ><img src='/_images/b_drop.png' alt='Delete' title='Delete'></span></div>";
													}else{
														$comment_delete = '';
													}
													$comment_update_box ='<div id="comment_id_li_body_'.$comment_id.'" class="panel" >
															
															<textarea id="comment_id_li_textarea_'.$comment_id.'"  class ="field_large" stle=" padding-bottom: 12px;">'.htmlentities($text,ENT_NOQUOTES, 'UTF-8').'</textarea>
															<div class="new_comment_actions">
															<br/>															
															<button onclick="updateComment('.$comment_id.'); return false;" class="secondary" style="padding-left:190px;">
																<span>Update Comment</span>
															</button>
															</div>
															
															<input type="hidden" class="comment_id_li_comment_id" name="comment_id_li_comment_id[]"  value="'.$comment_id.'">
															<input type="hidden" id="comment_id_li_comment_time_'.$comment_id.'"  name="comment_id_li_comment_time_'.$comment_id.'"  value="'.$comment_data[$cx]['date'].'">
															</div>';
												}
											}
											 echo '<li id="comment_id_li_'.$comment_id.'">
												<img src="'.$comment_user_data[0]['user_img'].'" class="comment_photo" />
												<div class="comment_body">
													<p><strong>' .ucfirst($comment_user_data[0]['first_name']) .' ' .ucfirst($comment_user_data[0]['last_name']) .'</strong><br>
													<em>' .@date("D M j \a\\t g:i a", mktime(@$comment_time_part[0],@$comment_time_part[1],@$comment_time_part[2],@$comment_date_part[1],@$comment_date_part[2],@$comment_date_part[0])) .' '.$comment_delete.'</em>'.'</p>
												     <p id="comment_id_li_msg_'.$comment_id.'">'.$text_string.'</p>
													 '.$comment_update_box.'
											</li>';
											$counts++;
											

										}
										//P($comment_id_row);
										if(count($comment_id_row) > 0){
												@rsort($comment_id_row);
												//p($comment_id_row);
												$last_comment_id  = $comment_id_row[0]; 
											}
									}
									
								echo '<li style="border-bottom:none;display:none;"><input type="hidden" id="last_comment_id" name="last_comment_id" value="'.$last_comment_id.'"></li></ul>
								
								
								<div class="clearer"></div>
							</div>
						</div>
					</div>
					<!--=========== COLUMN BREAK ===========-->
					<div class="workorder_content_col4">
						<div class="side_bucket_container bucket_container_last">

							<div class="side_bucket_title">CC List</div>
							<div class="side_bucket_content">';
								if(isset($_REQUEST['wo_id']) || isset($_REQUEST['copyWO'])) {
									echo WoDisplay::getCCList($wo_data[0]['id']);
								} else {
									echo '<input type="hidden" name="cclist" id="cclist" value="" />';
								}
								echo '<ul id="cc_list">';
								if(isset($_REQUEST['wo_id']) || isset($_REQUEST['copyWO'])) {
										$cclist = explode(",", $wo_data[0]['cclist']);
										
										for($lstx = 0; $lstx < sizeof($cclist); $lstx++) {
											if(!empty($cclist[$lstx])) {
												$cc_user_data = WoDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$cclist[$lstx] ."' WHERE active ='1' AND deleted='0'");
												if(count($cc_user_data[0]) > 0){
												echo '<li><div class="cclist_name">'
														.ucfirst($cc_user_data[0]['first_name']) .' ' .$cc_user_data[0]['last_name']
													.'</div>
													<button class="status cclist_remover ' . $wo_archive_status . '" onclick="removeCcUser('
														.$cclist[$lstx]
													.'); return false;"><span>remove</span></button>
												</li>';
											}
											}
										}
									}
								echo '</ul>
								<div class="cclist_actions ' . $wo_archive_status . '" id="add_cc">
									<button class="secondary" onclick="$(\'#add_cc\').css({display:\'none\'});$(\'#select_cc\').css({display:\'block\'}); return false;"><span>+ Add Person to CC List</span></button>
								</div>
								<div class="cclist_actions" id="select_cc" style="display: none;">
									<select name="cc_user" id="cc_user" style="margin-bottom:2px;">';
										if(isset($_REQUEST['wo_id'])) {
											echo WoDisplay::getUserOptionEditHTML();
										} else {
											echo WoDisplay::getUserOptionEditHTML();
										}
									echo '</select>
									<button class="secondary" style="clear:left; margin-left:10px;" onclick="addCcUser(); $(\'#select_cc\').css({display:\'none\'});$(\'#add_cc\').css({display:\'block\'}); return false;"><span>Add</span></button>
									<button class="cancel" onclick="$(\'#add_cc\').css({display:\'block\'}); $(\'#select_cc\').css({display:\'none\'}); return false;"><span>Cancel</span></button>
								</div>
								<div class="clearer"></div>
							</div>
						</div>
					</div>

					<!--=========== COLUMN BREAK ===========-->
				</div>

				<div class="clearer"></div>
					</body>
				<script>
					$(function() {
					   $("input[name=time_sensitive]").attr("checked", true);
                        showHideTime();
                        $(".date_picker").datepicker({
                        numberOfMonths: 2,
                        showOn: "both",
						buttonImage: "/_images/date_picker_trigger.gif", 
						buttonImageOnly: true 
						});
                    });
				</script>
				
				<!--==| END: Bucket |==-->
				<div class="message_cancel message_close message_required message_workorder_audit message_reopen request_type_msg severity1_msg severity2_msg severity3_msg request_type_msg message_outage_submit" style="width:3000px;height:1024px;position:fixed;background-color:#fffff;z-index:1.0;margin-top:-500px;margin-left:-630px;opacity: 0.3;filter:alpha(opacity=30);zoom:1.0;"></div>
				<div class="message_required">
					<p>
						Please fill in the required fields?
					</p>					
					<div style="clear: both;"></div>
					
					<div class="duplicate_buttons">
						<button onClick="$(\'.message_required\').css({display:\'none\'}); return false;"><span>OK</span></button>
						<div style="clear: both;"></div>
					</div>
				</div>
			</div>
			<div class="clearer"></div>
			</div>
			<div class="message_cancel">
				<p>
					You are about to leave this workorder. Any unsaved information will be lost.<br />Do you want to continue?
				</p>
				<input type="hidden" name="archive_project_confirm" id="archive_project_confirm" value="" />
				
				<div style="clear: both;"></div>
				
				<div class="duplicate_buttons">
					<button onClick="document.getElementById(\'prompt_save\').value = 1;document.location = \'/workorders/\'; return false;"><span>Yes</span></button> 
					<button class="cancel" onClick="$(\'.message_cancel\').css({display:\'none\'}); return false;"><span>No</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>


			<div class="message_workorder_audit">
				<h3>
					Work Order Audit</h3><br><p>';
				/*LH24787
				$audit_user_list = array();
				$audit_status = array();
				$request_types_array = array(2=>"Report a Problem",1=>"Report an Outage",3=>"Submit a Request");
				foreach($workorder_audit as $row )
				{
					$str_date = strtotime($row['log_date']);
					if(empty($audit_user_list[$row['log_user_id']]))
					{
						$audit_user_data = WoDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$row['log_user_id'] ."' LIMIT 1");
						$audit_user_list[$row['log_user_id']] = $audit_user_data[0]['first_name'] ." " .@$audit_user_data[0]['last_name'];
					}

					if(empty($audit_user_list[$row['assign_user_id']]))
					{
						$audit_user_data = WoDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$row['assign_user_id'] ."' LIMIT 1");
						$audit_user_list[$row['assign_user_id']] = $audit_user_data[0]['first_name'] ." " .@$audit_user_data[0]['last_name'];
					}

					if(empty($audit_status[$row['status']]))
					{
						$audit_status_data = WoDisplay::getQuery("SELECT * FROM `lnk_workorder_status_types` WHERE `id`='" .$row['status'] ."' LIMIT 1");
						$audit_status[$row['status']] = $audit_status_data[0]['name'] ;
					}

					if($row['audit_id']=='1')
					{
						echo "- ".$row['name']." created by ".$audit_user_list[$row['log_user_id']]." on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}
					else if($row['audit_id']=='2')
					{
						echo "- "."Work order ".$row['name']." ".$audit_user_list[$row['assign_user_id']]." and is in ".$audit_status[$row['status']]." status on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}
					else if($row['audit_id']=='3')
					{
						echo "- "."Work order ".$row['name']." to ".$audit_status[$row['status']]." by ".$audit_user_list[$row['log_user_id']]." on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}
					else if($row['audit_id']=='4')
					{
						echo "- ".$row['name']." by ".$audit_user_list[$row['log_user_id']]." and the Work order Status is ".$audit_status[$row['status']]." on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}
					if($row['Request_type'] != '')
					{	
						if($row['audit_id']=='6' && $prev_req != ''){
							echo "- "."Work order ".$row['name']." from ".$request_types_array[$prev_req]."  to ".$request_types_array[$row['Request_type']]." by ".$audit_user_list[$row['log_user_id']]." on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
						}
						$prev_req = $row['Request_type'];
					}
				}*/
				$audit_user_list = array();
				$audit_status = array();
				$request_types_array = array(2=>"Report a Problem",1=>"Report an Outage",3=>"Submit a Request");
				$old_status="";
				$old_status1="";
							
				
				function previous_status($id,$wo_id){
				$audit_status_data = WoDisplay::getQuery("SELECT * FROM `workorder_audit` WHERE id < '" .$id ."' and workorder_id='".$wo_id."'  order by  `id` DESC LIMIT 1");	
				$audit_status = $audit_status_data[0]['status'] ;
				$audit_status_dataold = WoDisplay::getQuery("SELECT * FROM `lnk_workorder_status_types` WHERE `id`='" .$audit_status ."' LIMIT 1");
				$audit_statusold[$row['status']] = $audit_status_dataold[0]['name'] ;	
				return $audit_statusold[$row['status']];
				}
				function previous_user($id,$wo_id){
				$audit_status_data = WoDisplay::getQuery("SELECT * FROM `workorder_audit` WHERE id < '" .$id ."' and workorder_id='".$wo_id."' order by `id` DESC LIMIT 1");	
				return $audit_status_data[0]['assign_user_id'];
				}
				foreach($workorder_audit as $row )
				{
					$str_date = strtotime($row['log_date']);
					if(empty($audit_user_list[$row['log_user_id']]))
					{
						$audit_user_data = WoDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$row['log_user_id'] ."' LIMIT 1");
						$audit_user_list[$row['log_user_id']] = $audit_user_data[0]['first_name'] ." " .@$audit_user_data[0]['last_name'];
					}

					if(empty($audit_user_list[$row['assign_user_id']]))
					{
						$audit_user_data = WoDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$row['assign_user_id'] ."' LIMIT 1");
						$audit_user_list[$row['assign_user_id']] = $audit_user_data[0]['first_name'] ." " .@$audit_user_data[0]['last_name'];
					}

					if(empty($audit_status[$row['status']]))
					{
						$audit_status_data = WoDisplay::getQuery("SELECT * FROM `lnk_workorder_status_types` WHERE `id`='" .$row['status'] ."' LIMIT 1");
						$audit_status[$row['status']] = $audit_status_data[0]['name'] ;	
					}
					$previous_status = previous_status($row['id'],$row['workorder_id']);	
					if($row['audit_id']=='1')
					{
						echo "- ".$row['name']." created by ".$audit_user_list[$row['log_user_id']]." on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}
					else if($row['audit_id']=='2')
					{							
						if(previous_user($row['id'],$row['workorder_id'])!= $row['assign_user_id'] && $audit_status[$row['status']]!= $previous_status){
							echo "- "."Work order ".$row['name']." ".$audit_user_list[$row['assign_user_id']]." by ".$audit_user_list[$row['log_user_id']]." and Status change from  ".$previous_status." to ".$audit_status[$row['status']]." status on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
						}else{
							echo "- "."Work order ".$row['name']." ".$audit_user_list[$row['assign_user_id']]." by ".$audit_user_list[$row['log_user_id']]." and  ".$audit_status[$row['status']]." status on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
						}
					$old_status = previous_status($row['id'],$row['workorder_id']);
					$old_status1 = $audit_status[$row['status']];						
					}
					else if($row['audit_id']=='3')
					{
						echo "- "."Work order ".$row['name']." to ".$audit_status[$row['status']]." by ".$audit_user_list[$row['log_user_id']]." on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}
					else if($row['audit_id']=='4')
					{
						echo "- ".$row['name']." by ".$audit_user_list[$row['log_user_id']]." and the Work order Status is ".$audit_status[$row['status']]." on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}
				   else if($row['audit_id']=='7')
					{
						if(previous_user($row['id'],$row['workorder_id'])!= $row['assign_user_id'] && $audit_status[$row['status']]!= $previous_status){
							echo "- "."Work order ".$row['name']." ".$audit_user_list[$row['assign_user_id']]." by ".$audit_user_list[$row['log_user_id']]." and Status change from  ".$previous_status." to ".$audit_status[$row['status']]." status on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}else 
						{
						echo "- "."Work order ".$row['name']." ".$audit_user_list[$row['assign_user_id']]." by ".$audit_user_list[$row['log_user_id']]." and  ".$audit_status[$row['status']]." status on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
						}
					}
					else if($row['audit_id']=='8')
					{
						echo "- ".$row['name']." by ".$audit_user_list[$row['log_user_id']]." and the Work order Status is ".$audit_status[$row['status']]." on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}
					else if($row['audit_id']=='9')
					{
						echo "- ".$row['name']." by ".$audit_user_list[$row['log_user_id']]." and the Work order Status is ".$audit_status[$row['status']]." on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}
					//Audit log for chnage request log
					else if($row['audit_id']=='10')
					{
						$audit_date_log = array();
						$audit_date_log = WoDisplay::getQuery("SELECT * FROM `workorder_date_log` WHERE `audit_id`='" .$row['id'] ."' AND wid = '".$wo_id."' LIMIT 1");
						if(count($audit_date_log[0]) > 0){
							echo "- Work Order Due Date changed from ".date("Y-m-d h:i:s A",strtotime($audit_date_log[0]['previous_launch_date']))." to ".date("Y-m-d h:i:s A",strtotime($audit_date_log[0]['new_launch_date']))." by ".$audit_user_list[$row['log_user_id']]."<br />";
						
						
						}
						
					}			
					//echo "- "."Work order ".$row['name']." to ".$audit_status[$row['status']]." by ".$audit_user_list[$row['log_user_id']]." on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}
					if($row['Request_type'] != '')
					{	
						if($row['audit_id']=='6' && $prev_req != ''){
							if(previous_user($row['id'],$row['workorder_id'])!= $row['assign_user_id'] && $audit_status[$row['status']]!= $previous_status){
						echo "- "."Work order ".$row['name']." ".$audit_user_list[$row['assign_user_id']]." by ".$audit_user_list[$row['log_user_id']]." and Status change from  ".$previous_status." to ".$audit_status[$row['status']]." status on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}else {echo "- "."Work order ".$row['name']." ".$audit_user_list[$row['assign_user_id']]." by ".$audit_user_list[$row['log_user_id']]." and  ".$audit_status[$row['status']]." status on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
					}
				
							//echo "- "."Work order ".$row['name']." from ".$request_types_array[$prev_req]."  to ".$request_types_array[$row['Request_type']]." by ".$audit_user_list[$row['log_user_id']]." on ".Date('Y-m-d h:i:s A', $str_date)."<br>";
						}
						$prev_req = $row['Request_type'];
					}
				echo '</p>
				<input type="hidden" name="archive_project_confirm" id="archive_project_confirm" value="" />
				
				<div style="clear: both;"></div>
				
				<div class="duplicate_buttons">			
					<button class="cancel" onClick="$(\'.message_workorder_audit\').css({display:\'none\'}); return false;"><span>Close</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>

			<div class="message_outage_submit">
				<p>
					Are you sure you want to report this outage? Reporting this outage will alert multiple members of both the IT Infrastructure and Digital Products and Services teams.  Please make sure you are reporting a confirmed outage, otherwise, please submit this as a Problem and the DPS Support team will work quickly to address it.
				</p>				
				<div style="clear: both;"></div>
				
				<div class="duplicate_buttons">
					<button onClick="saveWorkOrder(); return false;"><span>Yes</span></button>
					<button class="cancel" onClick="$(\'.message_outage_submit\').css({display:\'none\'}); return false;"><span>No</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>

			<div class="message_close">
				<p>
					You are about to close this work order.<br />Do you want to continue?
				</p>
				<input type="hidden" name="archive_project_confirm" id="archive_project_confirm" value="" />
				
				<div style="clear: both;"></div>
				
				<div class="duplicate_buttons">
					<button onClick="closeWorkOrder(); return false;"><span>Yes</span></button> 
					<button class="cancel" onClick="$(\'.message_close\').css({display:\'none\'}); return false;"><span>No</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>

			<div class="request_type_msg">
				<h3>
					 Outage <br> </h3>
					<p>					
					Any problem in a <u>PRODUCTION ENVIRONMENT</u> where more than 1 user cannot access or use an application <br>							
					<div style="float: left;border-right:3px solid #FFFFFF;width:365px;" >
					<p style="text-align:center;font-size:16px;"><u>Examples</u> <br></p>
					<ul>
					<li>Application is Down <br>
					<li>Application Performance So Severely Degraded it is Unusable <br>
					<li>Revenue Impacting Bug on Site<br>
					</ul>
					</div>
					<div style="padding-right:5px;float:left;">			
					<p style="text-align:center;font-size:16px;"><u>SLA\'s</u> <br></p>
					<p><span style="font-size:15px;">Acknowledgement: </span> Under 15 Minutes </p>
					<p><span style="font-size:15px;">Resolution: </span> Under 2 Hours </p><br>						
					</div>
				</p>
				
				<div style="clear: both;"></div>
				
				<div class="outage_msg_buttons">
					<button onClick="$(\'.request_type_msg\').css({display:\'none\'}); return false;"><span>Continue</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>

			<div class="severity1_msg">
				<h3>
					 Severity 1 <br> </h3>
											
					<div style="float: left;border-right:3px solid #FFFFFF;width:365px;" >
					<p style="text-align:center;font-size:16px;"><u>Examples</u> <br></p>
					<ul>
					<li> Application Performance Degraded but Limited User Impact<br>
					<li> Bug Effecting Large Functionality of Web Site<br> <br>
					</ul>
					</div>
					<div style="padding-right:5px;float:left;">			
					<p style="text-align:center;font-size:16px;"><u>SLA\'s</u> <br></p>
					<p><span style="font-size:15px;">Acknowledgement: </span> Under 2 Hours </p>
					<p><span style="font-size:15px;">Resolution: </span> Under 4 Hours </p><br>					
					</div>
				</p>
				
				<div style="clear: both;"></div>
				
				<div class="outage_msg_buttons">
					<button onClick="$(\'.severity1_msg\').css({display:\'none\'}); return false;"><span>Continue</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>

			<div class="severity2_msg">
				<h3>
					 Severity 2 <br> </h3>
											
					<div style="float: left;border-right:3px solid #FFFFFF;width:365px;" >
					<p style="text-align:center;font-size:16px;"><u>Examples</u> <br></p>
					<ul>
					<li> End User Queries<br>
					<li> Bug Effecting Small Functionality of Web Site<br> 
					<li> User Administration (i.e. User ID Creation/Deletion)<br> 
					</ul>
					</div>
					<div style="padding-right:5px;float:left;">			
					<p style="text-align:center;font-size:16px;"><u>SLA\'s</u> <br></p>
					<p><span style="font-size:15px;">Acknowledgement: </span> Under 8 Hours  </p>
					<p><span style="font-size:15px;">Resolution: </span> Under 48 Hours </p><br>						
					</div>
				</p>
				
				<div style="clear: both;"></div>
				
				<div class="outage_msg_buttons">
					<button onClick="$(\'.severity2_msg\').css({display:\'none\'}); return false;"><span>Continue</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>

			<div class="severity3_msg">
				<h3>
					 Severity 3 <br> </h3>
											
					<div style="float: left;border-right:3px solid #FFFFFF;width:365px;" >
					<p style="text-align:center;font-size:16px;"><u>Examples</u> <br></p>
					<ul>
					<li> Problem with minimal impact that can be addressed at the convenience of the team
						<br>
					<br><br><br>
					</ul>
					</div>
					<div style="padding-right:5px;float:left;">			
					<p style="text-align:center;font-size:16px;"><u>SLA\'s</u> <br></p>
					<p><span style="font-size:15px;">Acknowledgement: </span> Best Effort </p>
					<p><span style="font-size:15px;">Resolution: </span> Best Effort </p><br>						
					</div>
				</p>
				
				<div style="clear: both;"></div>
				
				<div class="outage_msg_buttons">
					<button onClick="$(\'.severity3_msg\').css({display:\'none\'}); return false;"><span>Continue</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>

			<div class="message_reopen">
				<p>
					You are about to reopen this work order.<br />Do you want to continue?
				</p>			
				<div style="clear: both;"></div>
				
				<div class="duplicate_buttons">
					<button onClick="reOpenWorkOrder(); return false;"><span>Yes</span></button> 
					<button class="cancel" onClick="$(\'.message_reopen\').css({display:\'none\'}); return false;"><span>No</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>';
           	
		}
		public function editAction() {
			
			$this->createAction();
		}
		public function listAction(){
			$from_action = true;
			include_once(WEBPATH."/html/_ajaxphp/workorder_json.php");
			$this->view->wo_details = $postingList;
		}
		public function mobileeditAction(){
			$readonly = false;
			$vars = array();
			$wo_id = ($_REQUEST['wo_id']);
			$user_id = $_SESSION["user_id"];
			$save = "";
			if(array_key_exists("save_type", $_POST)){
				$save = $_POST["save_type"];
			}
			switch($save){
				case "save_wo":
						$new_status = $_POST["wo_status"];
						$new_assigned = $_POST["wo_assigned_user"];

						$old_wo_data = WoDisplay::getQuery("SELECT * FROM `workorders` WHERE `id`='$wo_id' LIMIT 1");
						$assigned_date = "";
						if($old_wo_data[0]['assigned_to'] != $new_assigned){
							$assigned_date = ", `assigned_date`=NOW() ";
							$new_status = "2";
						}
						$update_wo_sql = "UPDATE `workorders` SET `assigned_to`='$new_assigned', `status`='$new_status' $assigned_date WHERE `id`='$wo_id'";
						$sql = $update_wo_sql;
						WoDisplay::executeQuery($update_wo_sql);
						
						if($new_status == "2"){
							// When a wo is assigned to a new person.
							$this->sendEmail("assigned", $wo_id, $user_id);
						}else{
							//change in status
							$this->sendEmail("status_change", $wo_id, $user_id);
						}
						break;
				case "close_wo":
						$close_wo_sql = "UPDATE `workorders` SET `closed_date`=NOW(), `status`='1' WHERE `id`='$wo_id'";
						$sql = $close_wo_sql;
						WoDisplay::executeQuery($close_wo_sql);
						$this->sendEmail("status_change", $wo_id, $user_id);
						break;
				case "comment_wo":
						$new_comment = $_POST["comment"];
						$insert_wo_comment = "INSERT INTO `workorder_comments` (`workorder_id`,`user_id`,`comment`,`date`) VALUES ('$wo_id','$user_id','$new_comment',NOW())";
						$sql = $insert_wo_comment;
						WoDisplay::executeQuery($insert_wo_comment);
						$this->sendEmail("comment", $wo_id, $user_id, $new_comment);
						break;
				case "":
						$sql = "No case matching.";
						break;
			}

			$wo_data = WoDisplay::getQuery("SELECT * FROM `workorders` WHERE `id`='$wo_id' LIMIT 1");
			
			$requestors_data = WoDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" . $wo_data[0]['requested_by'] ."' LIMIT 1");
			$project_data = WoDisplay::getQuery("SELECT CONCAT(`project_code`, ' - ', `project_name`) AS name FROM `projects` WHERE `id`='" . $wo_data[0]['project_id'] . "' LIMIT 1");
			$priority_data = WoDisplay::getQuery("SELECT CONCAT(`name`, ' - ', `time`) AS priority FROM `lnk_workorder_priority_types` WHERE `id`='" . $wo_data[0]['priority'] . "' LIMIT 1");

			if(@$wo_data[0]['creation_date'] != "") {
				$start_date_time_part = explode(" ", @$wo_data[0]['creation_date']);
				$start_date_part = explode("-", @$start_date_time_part[0]);
				$start_date = @date("m/d/Y h:i A", mktime($start_time_part[0],$start_time_part[1],$start_time_part[2],$start_date_part[1],$start_date_part[2],$start_date_part[0]));
			} else {
				$start_date = '';
			}
			if(@$wo_data[0]['completed_date'] != "") {
				$completed_date_time_part = explode(" ", @$wo_data[0]['completed_date']);
				$completed_date_part = explode("-", @$completed_date_time_part[0]);
				$esti_date = @date("m/d/Y h:i A", mktime($completed_time_part[0],$completed_time_part[1],$completed_time_part[2],$completed_date_part[1],$completed_date_part[2],$completed_date_part[0]));
			} else {
				$esti_date = '';
			}
			if(@$wo_data[0]['closed_date'] != "") {
				$closed_date_time_part = explode(" ", @$wo_data[0]['closed_date']);
				$closed_date_part = explode("-", @$closed_date_time_part[0]);
				$close_date = @date("m/d/Y h:i A", mktime($closed_time_part[0],$closed_time_part[1],$closed_time_part[2],$closed_date_part[1],$closed_date_part[2],$closed_date_part[0]));
			} else {
				$close_date = '';
			}

			$vars['project_name'] = $project_data[0]['name'];
			$vars['priority'] = $priority_data[0]['priority'];
			$vars['title'] = $wo_data[0]['title'];
			$vars['url'] = $wo_data[0]['example_url'];
			$vars['description'] = $wo_data[0]['body'];
			$vars['requested_user'] = $requestors_data[0]['first_name'] ." " .@$requestors_data[0]['last_name'];
			$vars['email'] = $requestors_data[0]['email'];
			$vars['contact'] = $requestors_data[0]['phone_office'];
			$vars['status'] = $wo_data[0]['status'];
			$vars['project_id'] = $wo_data[0]['project_id'];
			$vars['assigned_user'] = $wo_data[0]['assigned_to'];
			$vars['start_date'] = $start_date;
			$vars['completed_date'] = $esti_date;
			$vars['closed_date'] = $close_date;
			$vars['readonly'] = $readonly;

			$comment_data = WoDisplay::getQuery("SELECT * FROM `workorder_comments` WHERE `workorder_id`='" . $wo_id ."' order by date");
										
			for($cx = 0; $cx < sizeof($comment_data); $cx++) {
				$comment_date_time_part = explode(" ", $comment_data[$cx]['date']);
				$comment_date_part = explode("-", $comment_date_time_part[0]);
				$comment_time_part = explode(":", $comment_date_time_part[1]);											
				$comment_user_data = WoDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$comment_data[$cx]['user_id'] ."' LIMIT 1");

				$comment[$cx]['name'] = ucfirst($comment_user_data[0]['first_name']) .' ' .ucfirst($comment_user_data[0]['last_name']);
				$comment[$cx]['timestamp'] = @date("D M j \a\t g:i a", mktime(@$comment_time_part[0],@$comment_time_part[1],@$comment_time_part[2],@$comment_date_part[1],@$comment_date_part[2],@$comment_date_part[0]));
				$comment[$cx]['comment_text'] = nl2br(htmlentities($comment_data[$cx]['comment']));
			}

			$this->view->comment = $comment;
			$this->view->wo_id = $wo_id;
			$this->view->vars = $vars;
			$this->view->status = WoDisplay::getStatusOptionEditHTML($vars['status'], $vars['readonly'], $vars['project_id']);
			$this->view->assigned = WoDisplay::getUserAssignOptionEditHTML($vars['assigned_user'], $vars['readonly']);
//			$this->render("edit");
		}

		public function sendEmail($type, $woid, $userId, $comment_text=''){

			$select_email_users = "SELECT * FROM `workorders` WHERE `id`='$woid' LIMIT 1";
			$email_res = WoDisplay::getQuery($select_email_users);
			if(sizeOf($email_res) > 0) {
				$new_commenter = "SELECT * FROM `users` WHERE `id`='$userId' LIMIT 1";
				$commenter_res = WoDisplay::getQuery($new_commenter);
				$commenter_row = $commenter_res[0];
			
				$email_row = $email_res[0];
				
				$cc_list = $email_row['cclist'];
				$cc_list_part = explode(",", $cc_list);
				$at = $email_row['assigned_to'];
				$rb = $email_row['requested_by'];
				
				$users_email[$at] = true;
				$users_email[$rb] = true;
				
				for($e = 0; $e < sizeof($cc_list_part); $e++) {
					if(!empty($cc_list_part[$e])) {
						$users_email[$cc_list_part[$e]] = true;
					}
				}
				$user_keys = array_keys($users_email);

				$bc_id_query = "SELECT  `bcid`, `project_id`, `title`, `priority` FROM `workorders` WHERE `id`='" . $woid ."' LIMIT 1";
				$bc_id_result = WoDisplay::getQuery($bc_id_query);
				$bc_id_row = $bc_id_result[0];
				
				$select_priority = "SELECT * FROM `lnk_workorder_priority_types` WHERE `id`='" . $bc_id_row['priority'] ."'";
				$pri_res = WoDisplay::getQuery($select_priority);
				$pri_row = $pri_res[0];

				$select_project = "SELECT * FROM `projects` WHERE `id`='" . $bc_id_row['project_id'] ."'";
				$project_res = WoDisplay::getQuery($select_project);
				$project_row = $project_res[0];

				$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
				$company_res = WoDisplay::getQuery($select_company);
				$company_row = $company_res[0];

				$subject = "WO: " . $bc_id_row['title'] . " - Lighthouse Work Order Message";
				$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
				$headers = 'From: '.WO_EMAIL_FROM.'';

				switch($type){
					case 'assigned':
							$sendList = array($email_row['assigned_to'], $email_row['requested_by']);
							$file_list = "";
							$select_file = "SELECT * FROM `workorder_files` WHERE workorder_id='" . $woid . "' order by upload_date desc";
							$file_res = WoDisplay::getQuery($select_file);
							if(sizeOf($file_res) > 0) {
								$file_list = "\t-Attachment:\r\n";
								$fileCount = 1;
								foreach($file_res as $file_row){
									$file_list .= "\t\t" . $fileCount . ". " . $file_row['file_name'] . "\r\n\t\t   " . BASE_URL . "/files/" . $file_row['directory'] . "/" .$file_row['file_name'] . "\r\n";
									$fileCount += 1;
								}
							}
							$assignedTo = "";
							foreach($sendList as $user){
								$select_email_addr = "SELECT `email` FROM `users` WHERE `id`='" . $user ."' LIMIT 1";
								$email_addr_res = WoDisplay::getQuery($select_email_addr);
								$email_addr_row = $email_addr_res[0];
								if($user == $email_row['assigned_to']){
									$assignedTo = $user_row['email'];
								}
								$to = $email_addr_row['email'];
								$msg =  "Company: " . $company_row['name'] . "\r\n"
										."Project: " . $project_row['project_code'] ." - " . $project_row['project_name'] ."\r\n"
										."Link: " .BASE_URL ."/workorders/index/edit/?wo_id=" . $woid  ."\r\n\r\n"
										."WO [#" . $woid . "] has been assigned to " . $assignedTo . "\r\n\r\n"
										."\t-Priority: " . $pri_row['name'] ."-" . $pri_row['time'] ."\r\n"
										."\t-Description: " . $email_row['body'] ."\r\n"
										.$file_list . "\r\n"
										."..........................................................................";
								if(!empty($to)){
									mail($to, $subject, $msg, $headers);
								}
							}
							break;
					case 'status_change':
							if($email_row['status'] == '1' || $email_row['status'] == '3'){
								// When the WO is closed(1) or completed(3)
								if($email_row['status'] == '1')
									$woStatusText = 'closed';
								else if($email_row['status'] == '3')
									$woStatusText = 'completed';

								foreach($users_email as $user => $val){
									$select_email_addr = "SELECT `email` FROM `users` WHERE `id`='" . $user ."' LIMIT 1";
									$email_addr_res = WoDisplay::getQuery($select_email_addr);
									$email_addr_row = $email_addr_res[0];
									$to = $email_addr_row['email'];
									$msg =  "Company: " . $company_row['name'] . "\r\n"
											."Project: " . $project_row['project_code'] ." - " . $project_row['project_name'] ."\r\n"
											."Link: " .BASE_URL ."/workorders/index/edit/?wo_id=" . $woid  ."\r\n\r\n"
											."WO [#" . $woid . "] has been " . $woStatusText . " by " . $_SESSION['first'] . " ". $_SESSION['last'] . "\r\n\r\n"
											."\t-Priority: " . $pri_row['name'] ."-" . $pri_row['time'] ."\r\n"
											."\t-Description: " . $email_row['body'] ."\r\n\r\n"
											."..........................................................................";
									if(!empty($to)){
										mail($to, $subject, $msg, $headers);
									}
								}
							}
							break;
					case 'comment':
							$headers = 'From: ' . $commenter_row['email'];
							for($u = 0; $u < sizeof($user_keys); $u++) {
								$select_email_addr = "SELECT `email` FROM `users` WHERE `id`='" .$user_keys[$u] ."' LIMIT 1";
								$email_addr_res = WoDisplay::getQuery($select_email_addr);
								$email_addr_row = $email_addr_res[0];
								
								$to = $email_addr_row['email'];
								
								$msg = "Company: " . $company_row['name'] . "\r\n"
										."Project: " .$project_row['project_code'] ." - " .$project_row['project_name'] ."\r\n"
										."Link: " .BASE_URL ."/workorders/index/edit/?wo_id=" . $woid  ."\r\n\r\n"
										.ucfirst($commenter_row['first_name']) ." " .ucfirst($commenter_row['last_name']) ." commented on work order [#" . $woid . "]\r\n\r\n"
										."\t- Priority: " .$pri_row['name'] ."-" .$pri_row['time'] ."\r\n"
										."\t- Comment: " . strip_tags($comment_text, '<a><br><strong><ul><li><ol>') ."\r\n\r\n"
										."..........................................................................";
								
								
								if(!empty($to)) {
									mail($to, $subject, $msg, $headers);
								}
							}
							break;
					case '':
							break;
				}
			}
		}
	/**
		 * Ticket No #7927
		 * Calendar View of LH Enhancement
		 */
	function calendarviewAction()
		{
			//Get current Action name
	        $actionName = $this->getRequest()->getActionName();
			echo '<input type="hidden" name="curControllerName" id ="curControllerName" value="'.$actionName.'">';
			$cnt = 5;
			if($_SESSION['login_status'] == "client") {
				echo '<input type="hidden" name="client_login" id="client_login" value="client" />';
			} else {
				echo '<input type="hidden" name="client_login" id="client_login" value="employee" />';
			}
			$status_active = ($_REQUEST['status'] == '1') ? 'selected' : '';
			$status_archive = ($_REQUEST['status'] == '0') ? 'selected' : '';
			$status_draft = ($_REQUEST['status'] == '-1') ? 'selected' : '';
			echo '<!--=========== START: COLUMNS ===========-->
				<!--==| START: Bucket |==-->
					<div class="message_archive_select_check message_archive message_unarchive message_active" style="width:3000px;height:1024px;position:fixed;background-color:#ffffff;z-index:1;margin-top:-500px;margin-left:-630px;opacity: 0.3; filter: alpha(opacity = 30); zoom:1;"></div>
				<div class="main_actions_wo">
					
					<button onClick="window.location = \'/workorders/index/create/\';"><span>create new workorder</span></button>
					<form name="gotowo_form" onSubmit="javascript:return gotoWorkorder();">
						<input type="text" value="id #" onBlur="javascript:if (this.value == \'\') this.value = \'id #\';" onFocus="javascript:if (this.value == \'id #\') this.value=\'\';" class="field_xsmall" id="wo_id" name="wo_id"/>
						<span class="submit_button_span">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
						<input type="submit" value="go" class="submit_button">
					</form>
					<div id = "pagination" style="position:absolute;margin-left:290px;_margin:3px 0 0 30px;*margin:3px 0 0 30px;width:450px;display:none;"></div>
					<INPUT TYPE="hidden" ID="current_page_set" VALUE="1"/>
					<INPUT TYPE="hidden" ID="current_page" VALUE="1"/>
					<button onClick="return generateWOReport();" style="float:right;"><span>Generate Report</span></button>
					<button id="archiveBTN" onClick="archiveWO_CheckList();" style="float:right;"><span>Archive</span></button>
				</div>
						
				<!--==| END: Bucket |==-->

				<!--==| START: Bucket |==-->

				<div class="title_med workorders_filter">
					<label for="client_filter" id="client_filter_label">Client</label>
					<select id="client_filter" onchange="changeCompany();">
						<option value="-1">Show All</option>
					'.WoDisplay::getCompanyHTML().'
					</select>
					<label for="project_filter" id="project_filter_label">Project</label>
					<select id="project_filter" onchange="displayWorkorders(\'1\',\'first\',\'title\',\'1\',\'2\');">
						<option value="-1">Show All</option>
					</select>
					<label for="status_filter" id="status_filter_label">Status</label>
					<select id="status_filter" onchange="displayWorkorders(\'1\',\'first\',\'title\',\'1\');">
						<option value="-1">Show All</option>
						'.WoDisplay::getAllStatusOptionHTML().'
						<option value="over_due">Over Due</option>
						<option value="99">Open</option>
					</select>
					<label for="assigned_filter" id="assigned_filter_label">Assigned To</label>
					<select id="assigned_filter" onchange="displayWorkorders(\'1\',\'first\',\'title\',\'1\',\'2\');">
						<option value="-1">Show All</option>
					</select>
					<label for="project_status_filter" id="project_status_filter_label">Type</label>
					<select id="project_status_filter" onchange="getWO_On_Status();">
						<option value="1" '.$status_active.'>Active</option>
						<option value="0" '.$status_archive.'>Archived</option>
						<option value="-1" '.$status_draft.'>Draft</option>
					</select>
				</div>';
				$wo_data_cookie = isset($_COOKIE["lighthouse_wo_data"])? $_COOKIE["lighthouse_wo_data"] : "";
				$req_Type_Arr = Array();
				$req_Type_Arr['Outage'] = ' selected="selected"';
				$req_Type_Arr['Problem'] = ' selected="selected"';
				$req_Type_Arr['Request'] = ' selected="selected"';

				if(!empty($wo_data_cookie))
				{

					$wo_data_cookie_all = explode("~", @$wo_data_cookie);
					$requestTypeFilter = $wo_data_cookie_all[4];
					if(!empty($requestTypeFilter))
					{
						$req_Type_Arr = Array();
						$requestTypeFilter_all = explode(",", @$requestTypeFilter);
						for($u = 0; $u < sizeof($requestTypeFilter_all); $u++) {
							if(!empty($requestTypeFilter_all[$u]))
							{
								$req_Type_Arr[$requestTypeFilter_all[$u]] = ' selected="selected"';
							}
						}
					}
				}
				echo '<input type="hidden" name="currentMonthData" id="currentMonthData" value="'.date('n-Y').'">';
				echo '<div class="title_med2 workorders_filter">
				<INPUT TYPE="hidden" ID="requestTypeFilter" VALUE"" />
					<label for="project_status_filter" style="color: #fff;float: left; margin-top: 10px; font-size: 15px;" id="project_status_filter_label">Request Type</label>
					<select id="control_7" name="control_7[]" multiple="multiple" size="5">
						<option value=""></option>
						<option value="Outage" '.$req_Type_Arr['Outage'].'>Outage</option>
						<option value="Problem" '.$req_Type_Arr['Problem'].'>Problem</option>
						<option value="Request" '.$req_Type_Arr['Request'].'>Request</option>
				</select>';

				echo '<div class="title_med workorders_filter" style="position: inherit;align:center;">
				   <label style="padding-left:15px;" for="requestedby_filter" id="requestedby_filter_label">Request By</label>
					<select id="requestedby_filter" style="width: 140px;" onchange="displayWorkorders(\'1\',\'first\',\'title\',\'1\',\'2\');">
						<option value="-1">Show All</option>
					</select>
					</div>
					<!-- ==| START: view type Botton Divison#7927 |== -->

		          <div style="float: right; margin: 0pt; position: absolute; right: 12px; top: 85px;padding-right:11px;" id="list_view" >
		         	<img src="/_images/cal_dis_03.png" alt="Calendar view" title="Calendar view" width="23" height="23" style="padding-right:6px;" />
                           <a href="/workorders/index" title="List View"><img src="/_images/list_active.png" alt="List View" width="23" height="23" ></a>
		         </div>
		         <!-- ==| END: view type Botton Divison#7927 |== -->';

				$end_date_default = date("m/d/Y");// current date;
				$start_date_add_one_month = strtotime(date("m/d/Y", strtotime($end_date_default)) . "-1 month");
				$start_date_default = date("m/d/Y", $start_date_add_one_month);
        echo '
				<INPUT TYPE="hidden" ID="start_date_hidden" VALUE="'.$start_date_default.'" />
				<INPUT TYPE="hidden" ID="end_date_hidden" VALUE="'.$end_date_default.'" />
				<INPUT TYPE="hidden" ID="search_hidden" VALUE="" />
				  <div id = "search_and_date_filters" style="display: none; position: relative; float: left;">
          	<button onClick="displayWorkorders(\'1\',\'first\',\'title\',\'1\',\'1\');" style="float:right;margin-top:6px"><span>GO</span></button>  
            <div id="search_box" style="float:right;padding-left:30px;">
  					 <label style = "color:#FFFFFF;" class="small">Search</label>
  				    <input style = "margin-top:6px" type="text" value = "" id = "search_text"/>
  				  </div>
             
            <div class="right_actions datePick" id = "start_date_select" style = "margin-top:6px;padding-right:12px;padding-left:10px;float:left;_width:150px;*width:150px;">
  					 <label style = "color:#FFFFFF;float:left;padding:6px 5px 0 0;font-size:12px;" class="small">Start Date</label>
  					 <input type="text" style = "width:65px;float:left;" class="jumptodatecal date_picker field_small" id="start_date_input" value="'.$start_date_default.'"/>
            </div>
            
            <div class="right_actions datePick" id = "end_date_select" style = "margin-top:6px;padding:0 20px 0 5px;float:left;_width:150px;*width:150px;">
  					 <label style = "color:#FFFFFF;padding:6px 5px 0 0;float:left;font-size:12px;" class="small">End Date</label>
  					 <input type="text" style = "width:65px;float:left;" class="jumptodatecal date_picker field_small" id="end_date_input" value="'.$end_date_default.'"/>
            </div>
            
         </div>
        </div>
        <div class="message_cancel see_more_overlay popup_cal" style="width:3000px;height:1024px;position:fixed;background-color:#fffff;z-index:1.0;margin-top:-500px;margin-left:-630px;opacity: 0.3;filter:alpha(opacity=30);zoom:1.0;"></div>
        <!--==| START: Bucket |==-->
		<div class="month_controller_container">
			<ul class="month_controller" style="margin-left:175px;text-transform:none;">
				<li class="month_arrows"><button class="month_arrows month_arrows_left"></button></li>
				<li class="wo_month_controller_display" style="text-align:center;"> </li>
				<li class="month_arrows"><button class="month_arrows month_arrows_right"></button></li>
			</ul>
		</div>
		
        <!--==| START: Bucket |==-->
						<div class="resources_controller resources_controller_wide">
							<ul class="wo_days_container wo_days_container_wide">
								<li>Monday</li>
								<li>Tuesday</li>
								<li>Wednesday</li>
								<li>Thursday</li>
								<li>Friday</li>
								<li>Saturday</li>
								<li  style="background:none;">Sunday</li>
							</ul>
						</div>
												
					<!--=========== COLUMN BREAK ===========-->';
        echo '<div class="popup_cal">
        <h4>Change Calendar Display to <a href="#" onclick="$(\'.popup_cal\').css({display:\'none\'}); return false;"><img class="close" src="/_images/close_btn.gif" width="15" height="14" alt="close" /></a></h4>
  			<p><span class="title"> Month </span><select name="mon" id="mon_cal">
  					  '.$this->getMonth().'
					  </select>	
  			<span class="title"> Year</span> <select name="year_cal" id="year_cal">
  					  '.$this->getYear().'
					  </select>
  			
  			</p>
  			<div class="duplicate_buttons">
	  			<button  class="cancel" onclick="$(\'.popup_cal\').css({display:\'none\'}); return false;">
	  			<span>Cancel</span>
	  			</button>
	  			<button id="update_cal" >
	  			<span>Update</span>
	  			</button>
  			
  			</div>
        </div>';
        echo '<div class="wo_calender_view"></div>
        	<div  id="wo_dimmer_ajax_cal" class="wo_save_box">
				<img alt="ajax-loader" src="/_images/ajax-loader.gif"/>
			</div>
			<br/>
			<div class="color_block">
				<div class="color_status"><strong>STATUS</strong></div>
			 	<div class="color_blk"></div>
			 	<div class="color_blk2"><strong> NEW</strong></div>
			 	<div class="color_blk" style="background:#DAE7B2;"></div>
			 	<div class="color_blk2"><strong> FIXED</strong></div>
			 	<div class="color_blk" style="background:#EBCBCC;" ></div>
			 	<div class="color_blk2"><strong> REJECTED</strong></div>
			 	<div class="color_blk" style="background:#DDF2FF;"></div>
			 	<div class="color_blk2"><strong> HOLD</strong></div>
			 	<div class="color_blk" style="background:#FCFBF4;"></div>
			 	<div class="color_blk2" ><strong> IN PROGRESS/FEEDBACK PROVIDED/NEED MORE INFO</strong></div>
			</div>
        ';
        }
        
        function getMonth(){
        	$output = "";
        	$monthText[1] = "January";
			$monthText[2] = "February";
			$monthText[3] = "March";
			$monthText[4] = "April";
			$monthText[5] = "May";
			$monthText[6] = "June";
			$monthText[7] = "July";
			$monthText[8] = "August";
			$monthText[9] = "September";
			$monthText[10] = "October";
			$monthText[11] = "November";
			$monthText[12] = "December";
        	foreach($monthText as $month_key => $month_val){
        		$output .= "<option value=$month_key>".$month_val."</option>";
        	}
        	 return $output;
        }
        
	 function getYear(){
        	$output = "";
        	$yearText = array();
        	for($i=2009; $i<=2020; $i++){
        		$yearText[$i] = $i;
        	}
        	
        	
        	foreach($yearText as $year_key => $year_val){
        		$output .= "<option value=$year_key>".$year_val."</option>";
        	}
        	 return $output;
        }
		
		function dateDiffComment($commentDate){ 
				$dateDiff = array();
				
				

				$diff = abs(strtotime($commentDate) - time()); 

				$years   = floor($diff / (365*60*60*24)); 
				$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
				$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

				$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 

				$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 

				$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));
				$dateDiff['years'] = $years;
				$dateDiff['months'] = $months;
				$dateDiff['days'] = $days;
				$dateDiff['hours'] = $hours;					
				$dateDiff['minuts'] = $minuts;
				$dateDiff['seconds'] = $seconds;		
				return $dateDiff;
						
			}

		public function requestorselectAction(){
			if(isset($_REQUEST['wid'])) {
					echo WoDisplay::getUserOptionEditHTML($_REQUEST['woRequestedByPrev']);
				} else {
					echo WoDisplay::getUserOptionHTML();
				}
			$this->_helper->layout->disableLayout();
		}
		
		public function projectselectAction(){
			$proj_select = isset($_COOKIE["lighthouse_create_wo_data"])? $_COOKIE["lighthouse_create_wo_data"] : "";
			if((isset($_REQUEST['wid']) &&(!empty($_REQUEST['wid']))) || (isset($_REQUEST['copyWO']) && (!empty($_REQUEST['copyWO'])))) {
			//if(isset($_REQUEST['wid']) || isset($_REQUEST['copyWO'])) {
				echo WoDisplay::getProjectOptionHTML($_REQUEST['project_id']);
			} else if($proj_select != ""){
				echo WoDisplay::getProjectOptionHTML($proj_select);
			}else {  
				$pj = @$_REQUEST['project'];
				echo WoDisplay::getProjectOptionHTML($pj);
			}
		$this->_helper->layout->disableLayout();
		}
		
		
		public function projectssoselectAction(){
			$proj_select = isset($_COOKIE["lighthouse_create_wo_data"])? $_COOKIE["lighthouse_create_wo_data"] : "";
			if((isset($_REQUEST['wid']) &&(!empty($_REQUEST['wid']))) || (isset($_REQUEST['copyWO']) && (!empty($_REQUEST['copyWO'])))) {
			//if(isset($_REQUEST['wid']) || isset($_REQUEST['copyWO'])) {
				echo WoDisplay::getssoProjectOptionHTML($_REQUEST['project_id']);
			} else if($proj_select != ""){
				echo WoDisplay::getssoProjectOptionHTML($proj_select);
			}else {  
				$pj = @$_REQUEST['project'];
				echo WoDisplay::getssoProjectOptionHTML($pj);
			}
			$this->_helper->layout->disableLayout();
		
		
		}
		
		
		public function wostatusAction(){
		
			$wid = $_REQUEST['woId'];
			$wo_status = $_REQUEST['woStatus'];
			$wo_data = WoDisplay::getQuery("SELECT status FROM `workorders` WHERE `id`='$wid' LIMIT 1");
			//print_r($wo_data);
			if($wo_status !=  $wo_data[0]['status']){ 
				$new_status = $wo_data[0]['status'];
				//$workorder_audit = WoDisplay::getQuery("SELECT wa.*,at.name  FROM `workorder_audit` wa,`lnk_audit_trial_types` at where workorder_id = '$wid' and at.id = wa.audit_id order by `log_date`");
				//$audit_status = $workorder_audit[0]['status'] ;
				//echo "SELECT * FROM `lnk_workorder_status_types` WHERE `id`='" .$new_status ."' LIMIT 1";
				$audit_status_dataold = WoDisplay::getQuery("SELECT * FROM `lnk_workorder_status_types` WHERE `id`='" .$new_status ."' LIMIT 1");
				//$audit_statusold[$row['status']] = $audit_status_dataold[0]['name'] ;
				echo "Your Comment has been saved with status ".$audit_status_dataold[0]['name'] ;
			}
			$this->_helper->layout->disableLayout();
		
		
		}
		
		public function wostatusupdateAction(){
		
			$wid = $_REQUEST['woId'];
			$wo_status = $_REQUEST['woStatus'];
			$wo_data = WoDisplay::getQuery("SELECT * FROM `workorders` WHERE `id`='$wid' LIMIT 1");
			//print_r($wo_data);
			if($wo_status !=  $wo_data[0]['status']){ 
				echo $new_status = $wo_data[0]['status']."~".$wo_data[0]['assigned_to'];
				
			}
			$this->_helper->layout->disableLayout();
		
		
		}
		
		
		
		
		
}		       	


