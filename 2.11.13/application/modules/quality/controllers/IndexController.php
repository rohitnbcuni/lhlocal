<?PHP
	include('Quality.inc');
	
	class Quality_IndexController extends LighthouseController  { 
		public function indexAction() {
			if($_SESSION['login_status'] == "client") {
				echo '<input type="hidden" name="client_login" id="client_login" value="client" />';
			} else {
				echo '<input type="hidden" name="client_login" id="client_login" value="employee" />';
			}
			echo '<!--=========== START: COLUMNS ===========-->
				<!--==| START: Bucket |==-->
			<div class="message_archive_select_check message_archive message_unarchive" style="width:3000px;height:1024px;position:fixed;background-color:#ffffff;z-index:1;margin-top:-500px;margin-left:-630px;opacity: 0.3; filter: alpha(opacity = 30); zoom:1;"></div>
				<div class="main_actions_wo">
					<button onClick="return CreateDefect();"><span>create new defect</span></button>
					<form name="gotowo_form" onSubmit="javascript:return gotoWorkorder();">
						<input type="text" value="id #" onBlur="javascript:if (this.value == \'\') this.value = \'id #\';" onFocus="javascript:if (this.value == \'id #\') this.value=\'\';" class="field_xsmall" id="defect_id" name="defect_id"/>
						<span class="submit_button_span">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
						<input type="submit" value="go" class="submit_button">
					</form>
					<button onClick="return generateWOReport();" style="float:right;"><span>Generate Report</span></button>
					<button id="archiveBTN" onClick="archiveWO_CheckList();" style="float:right;display:none;"><span>Archive</span></button>
				</div>
				<!--==| END: Bucket |==-->

				<!--==| START: Bucket |==-->

				<div class="title_med workorders_filter">
					<label for="client_filter" id="client_filter_label">Client</label>
					<select id="client_filter" style="width:115px" onchange="changeCompany();">
						<option value="-1">Show All</option>
					'.QaDisplay::getCompanyHTML().'
					</select>
					<label for="project_filter" id="project_filter_label">Project</label>
					<select id="project_filter" >
						<option value="-1">Show All</option>
					</select>

					<label for="status_filter" id="status_filter_label">Status</label>
					<select id="status_filter" >
						<option value="-1">Show All</option>
						<option value="99">Not Closed</option>
						'.QaDisplay::getAllStatusOptionHTML().'
					</select>
					<label for="project_severity_filter" id="project_status_filter_label">Severity</label>
					<select id="severity_filter" > 
						<option value="-1">Show All</option>
						'.QaDisplay::getcustomDropDown("QA_SEVERITY").'
					</select>
					<label for="assigned_filter" id="assigned_filter_label">Assigned To</label>
					<select id="assigned_filter"  style="width:100px;">
						<option value="-1">Show All</option>
					</select>
					
					<a href="javascript:void(null);" onclick="qulaityFilterJson();"><img src="../_images/quality_refresh.png" alt="refresh" title="refresh" style="vertical-align:middle;margin-top:9px;" /></a>

				</div>

				<!--==| END: Bucket |==-->
				
				<!--==| START: Sorting |==-->
				<ul class="project_filters quality_sort" id="quality_sort" style="padding-left:8px; width:952px;">
					<li class="id"><a href="#" class="down" id="idsort" onClick="sortQuality(\'id\'); return false;">Defect ID</a></li>
					<li class="title"><a href="#" id="titlesort" onClick="sortQuality(\'title\'); return false;">title</a></li>
					<li class="severity"><a id="severitysort" href="#" onClick="sortQuality(\'severity\'); return false;">severity</a></li>
					<li class="status"><a id="statussort" href="#" onClick="sortQuality(\'status\'); return false;">status</a></li>
					<li class="category"><a id="categorysort" href="#" onClick="sortQuality(\'category\'); return false;">category</a></li>
					<li class="version"><a id="versionsort" href="#" onClick="sortQuality(\'version\'); return false;">version</a></li>
					<li class="opendate"><a id="open_datesort" href="#" onClick="sortQuality(\'open_date\'); return false;">open date</a></li>
					<li class="assigned"><a id="assigned_tosort" href="#" onClick="sortQuality(\'assigned_to\'); return false;">assigned to</a></li>
					<li class="detected_by"><a id="detected_bysort" href="#" onClick="sortQuality(\'detected_by\'); return false;">detected by</a></li>
					<li class="last_action"><a id="last_actionsort" href="#" onClick="sortQuality(\'last_action\'); return false;">Last Action</a></li>
				</ul>

				<!--==| END: Sorting |==-->

				<!--==| START: Work Orders |==-->
				<input type="hidden" name="active_wo" id="active_wo" value="" />
				<div id="wo_containter" class="workorders_container">';
					
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


			<div style="display: none;" id="wo_dimmer_ajax" class="wo_save_box">
				<img alt="ajax-loader" src="/_images/ajax-loader.gif"/>
			</div>

			<div class="message_archive_select_check">
				<p>
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
		
			

			$hideStyle = "";
			$readonly = false;

			$wo_archive_status = "";
			$wo_archive_text = "";
			$proj_select = "";
			$option_SEVERITY ='_blank';
			$option_INFRA_TYPE ='_blank';
			$pj_cookie = "";
			$category_cookie = "";
			$severity_cookie = "";
			$assigned_to_cookie = "";
			$example_url_cookie = "";
			$origin_cookie = "";
			$os_cookie = "";
			$browser_cookie = "";
			$version_cookie = "";

			if(isset($_GET['defect_id'])) {
				$defect_id = ($_GET['defect_id']);
				/* LH fixes
				 * LH#21355
				 */
				if(!is_numeric($defect_id )){
					$this->_redirect("quality/index/");
				}
				$wo_data = QaDisplay::getQuery("SELECT * FROM `qa_defects` WHERE `id`='$defect_id' LIMIT 1");			
				/* LH fixes
				 * LH#21355
				 */
				if(count($wo_data) == 0){
					$this->_redirect("quality/index/");
				}
				$workorder_audit = QaDisplay::getQuery("SELECT wa.*,at.name  FROM `quality_audit` wa,`lnk_audit_trial_types` at where defect_id = '$defect_id' and at.id = wa.audit_id order by `log_date`");				

				$requestors_data = QaDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$wo_data[0]['requested_by'] ."' LIMIT 1");
				
				if($wo_data[0]['archived'] == '1'){
					$wo_archive_status = " wo_archived ";
					$wo_archive_text = " readonly ";
				}
				
				$start_date_time_part = explode(" ", @$wo_data[0]['creation_date']);
				$start_date_part = explode("-", @$start_date_time_part[0]);
				$start_time_part = explode(":", @$start_date_time_part[1]);
				
				$completed_date_time_part = explode(" ", @$wo_data[0]['completed_date']);
				$completed_date_part = explode("-", @$completed_date_time_part[0]);
				$completed_time_part = explode(":", @$completed_date_time_part[1]);
				
				$closed_date_time_part = explode(" ", @$wo_data[0]['closed_date']);
				$closed_date_part = explode("-", @$closed_date_time_part[0]);
				$closed_time_part = explode(":", @$closed_date_time_part[1]);
				$pageLoadHide = 'style="display:block;"';
                } else {
				$defect_id = "";
				$proj_select = isset($_COOKIE["lighthouse_create_wo_data"])? $_COOKIE["lighthouse_create_wo_data"] : "";
				$pageLoadHide = 'style="display:none;"';
				$li_INFRA_TYPE = 'style="display:none;"';
				$li_CRITICAL = 'style="display:none;"';
				$qa_cookie_values = isset($_COOKIE["lighthouse_quality_create_defect"])? $_COOKIE["lighthouse_quality_create_defect"] : "";
				$qa_cookie_values = @$qa_cookie_values;
				
				if(!empty($qa_cookie_values))
				{
					// P:~A:~O:~OS:~B:~V
					$qa_cookie_part = explode("~",@$qa_cookie_values);
					
					$pj_cookie = strtok(@$qa_cookie_part[0], "P:");
					$category_cookie = strtok(@$qa_cookie_part[1], "C:");
					$severity_cookie = strtok(@$qa_cookie_part[2], "S:");
					$assigned_to_cookie = strtok(@$qa_cookie_part[3], "A:");
					$example_url_cookie_part = explode("URL:",@$qa_cookie_part[4]);
					$example_url_cookie = @$example_url_cookie_part[1];
					$origin_cookie = strtok(@$qa_cookie_part[5], "O:");
					$os_cookie = strtok(@$qa_cookie_part[6], "OS:");
					$browser_cookie = strtok(@$qa_cookie_part[7], "B:");
					$iteration_cookie = strtok(@$qa_cookie_part[8], "IT:");
					$product_cookie = strtok(@$qa_cookie_part[9], "PR:");
					$version_cookie = strtok(@$qa_cookie_part[10], "V:");
				}
				$qa_proj_cookie_values = isset($_COOKIE["lh_qa_project_cookie"])? $_COOKIE["lh_qa_project_cookie"] : "";
				if(!empty($qa_proj_cookie_values))
				{
					$pj_cookie = $qa_proj_cookie_values;
				}
			}
			$qa_selected_sort_cookie = isset($_COOKIE["selectedSortOption"])? $_COOKIE["selectedSortOption"] : "";
			if(!empty($qa_selected_sort_cookie)){
				$sort_option = explode(":",$qa_selected_sort_cookie);
			}		
      		
			$qa_data_cookie_values = isset($_COOKIE["lighthouse_qa_data"])? $_COOKIE["lighthouse_qa_data"] : "";
			if(!empty($qa_data_cookie_values)){
				$qa_list = explode("~",$qa_data_cookie_values);
				if(isset($_POST['stringId'])){
					$qa_navigation_values = $_POST['stringId'];
					$qa_id_array = explode(",",$qa_navigation_values);
				} else {
					$qa_id_list_result = QaDisplay::getQAIDs($qa_list[0],$qa_list[1],$qa_list[2],$qa_list[3],$qa_list[4],$sort_option[0],$sort_option[1],$wo_data[0]['archived']);
					$qa_id_array[0] = ""; 
					foreach($qa_id_list_result as $key=>$value){
						$qa_id_array[$key+1] = $qa_id_list_result[$key]['id'];
					} 
				}
				$defect_id_key = array_search($defect_id,$qa_id_array);
				if($wo_data[0]['archived'] == '0'){
				  if($defect_id_key == 1 || empty($defect_id)){
					$display_prev = "display:none";
				  } if($defect_id_key == count($qa_id_array)-1 || empty($defect_id)){
					$display_next = "display:none";
				  }
				  $qa_id_array_string = implode(",",$qa_id_array);
				
				} else {
					$start_key = (ceil(intval($defect_id_key)/3)-1)*3;
					$end_key = $start_key+3;
					$qa_id_array_string = "";
					$i = 0;
					for($i=$start_key+1;$i<=$end_key;$i++){
						$qa_id_array_string .=  ",".$qa_id_array[$i];
					}
					if($defect_id_key == $start_key+1 || empty($defect_id)){
						$display_prev = "display:none";
					} if($defect_id_key == count($qa_id_array)-1 || empty($defect_id)){
						$display_next = "display:none";
					}
				}
		//		$qa_id_array_string = implode(",",$qa_id_array);
				
				$prev_defect_id = $qa_id_array[$defect_id_key-1];
				$next_defect_id = $qa_id_array[$defect_id_key+1]; 
			} else {
				$display_prev = "display:none";			
				$display_next = "display:none";
			}
//			echo $qa_selected_sort_cookie;die();
      echo '<div class="column_full_workorders_QA">
				<!--==| START: Bucket |==-->
				<div class="title_med workorders_filter">
					<div class="title_actions"><button class="back_arrow" onClick="window.location= \'/quality/\';"><span>all defects</span></button></div>
					<h4>Defect Entry</h4>
					<form onsubmit="javascript:return qualityeditsearch();" name="gotowo_form">';
					?>
						<input type="text" name="defect_search_id" id="defect_search_id" class="field_xsmall" style="width:50px;height:17px;margin-right:3px;" onfocus="javascript:if (this.value == 'id #') this.value='';" onblur="javascript:if (this.value == '') this.value = 'id #';" value="id #">
						<span class="submit_button_span" style="margin-top:7px;margin-right:0px;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
						<input type="submit" class="submit_button" value="go" style="width:25px;margin-top:7px;margin-left:-11px;border:0">
					</form>
				<?php  echo '<form id = "prevNextNav" method = "POST">  
            <input name = "stringId" type = "hidden" id = "stringId" value = '.$qa_id_array_string.'>
            <button id="next_button" style = "float:right;margin-right:12px;'.$display_next.';margin-top: 7px;" onClick="navigate('.$next_defect_id.')"><span>NEXT</span></button>
					  <button id="prev_button" style = "float:right;'.$display_prev.';margin-top: 7px;" onClick="navigate('.$prev_defect_id.')"><span>PREV</span></button>					
          </form>
        </div>
				<div class="content">
          <div class="wo_dimmer" id="wo_dimmer" style="display:none;"></div>
					<div class="wo_save_box" id="wo_dimmer_ajax" style="display: none;"><img src="/_images/ajax-loader.gif" alt="ajax-loader"/></div>
					<!--<div class="wo_dimmer" id="wo_dimmer"></div>
					<div class="wo_save_box" id="wo_dimmer_ajax"><img src="/_images/ajax-loader.gif" alt="ajax-loader"/></div>-->
					<!--=========== COLUMN BREAK ===========-->
					<div class="workorder_content_col1">
						<input type="hidden" name="user_id" id="user_id" value="' .$_SESSION['user_id'] .'" />';
						if(!isset($_GET['defect_id'])) {
							echo '<img src="/_images/empty_mugshot.gif" id="assigned_user_img" class="qa_requestor_photo" style="margin-top:70px;margin-right:6px;position:absolute;margin-left:425px;margin-top:65px;">'; 
						}
						echo '<ul>';
						if(isset($_GET['defect_id'])) {
							echo '<li style="width:300px">
								<label for="defect_ID" id="wo_project_label">Defect ID:</label>
								<input type="text" value="'.$_GET['defect_id'].'" readonly="readonly" class="readonly" maxlength="6" size="6">
							</li>';
						}
						if(!isset($_GET['defect_id'])) {										
							echo '<li>
								<label for="wo_project" id="wo_project_label">Project:</label>
								<select class="field_medium" style="width:200px" name="wo_project" id="wo_project" onchange="updateVersion(this.value);updateCClist(this.value);">';							
								echo QaDisplay::getProjectOptionEditHTML($pj_cookie,'1');
								echo '</select>
								<input type="checkbox" '.((!empty($pj_cookie)) ? 'checked' : '').' title="Remember me" name="qa_project_remember" id="qa_project_remember" />
								<label style="text-align:left;padding-left:5px;width:50px">Remember</label>
							</li>';
						}
							echo '<li>
								<label for="wo_qa_category" id="wo_qa_category_label">Category:</label>
								<select class="field_medium" name="QA_CATEGORY" id="QA_CATEGORY" >
									<option value="_blank">--Select Category--</option>
								';
								if(isset($_GET['defect_id'])) {
									echo QaDisplay::getcustomDropDown("QA_CATEGORY",$wo_data[0]['category']);
									echo '</select>';
									echo '<input type="checkbox" '.((!empty($category_cookie)) ? 'checked' : '').' title="Remember me" name="qa_category_remember" style="display:none;" id="qa_category_remember" />';
								}
								else
								{
									echo QaDisplay::getcustomDropDown("QA_CATEGORY",$category_cookie);
									echo '</select>
									<input type="checkbox" '.((!empty($category_cookie)) ? 'checked' : '').' title="Remember me" name="qa_category_remember" id="qa_category_remember" />';
								}
								
							echo '</li>';
							
							echo'<li>
								<label for="wo_qa_severity" id="wo_qa_severity_label">Severity:</label>
								<select class="field_medium" name="QA_SEVERITY" id="QA_SEVERITY" >
									<option value="_blank">--Select Severity--</option>
								';
								if(isset($_GET['defect_id'])) {
									echo QaDisplay::getcustomDropDown("QA_SEVERITY",$wo_data[0]['severity']);
									echo '</select>';
									echo '<input type="checkbox" '.((!empty($severity_cookie)) ? 'checked' : '').' title="Remember me" name="qa_severity_remember" style = "display:none;" id="qa_severity_remember" />';
								}
								else
								{
									echo QaDisplay::getcustomDropDown("QA_SEVERITY",$severity_cookie);
									echo '</select>
									<input type="checkbox" '.((!empty($severity_cookie)) ? 'checked' : '').' title="Remember me" name="qa_severity_remember" id="qa_severity_remember" />';
								}
								
							echo '</li>';
							if(!isset($_GET['defect_id'])) {
							echo '<li>
								<label for="wo_assigned_user" id="wo_assigned_user_label" >Assigned To:</label>
								<select class="field_medium" name="wo_assigned_user" id="wo_assigned_user" onChange="changeImage(this.value);" >';
									echo QaDisplay::getUserAssignOptionEditHTML($assigned_to_cookie);
								echo '</select>
								<input type=checkbox '.((!empty($assigned_to_cookie)) ? 'checked' : '').' name="qa_assigned_remember" id="qa_assigned_remember" title="Remember me" />
							</li>';
							}
							echo '<li>
								<label for="wo_title" id="wo_title_label">Summary:</label>
								<input name="wo_title" id="wo_title" class="field_large" type="text" value="' . htmlspecialchars(@$wo_data[0]['title']) .'" />
							</li>
							<li>
								<label for="wo_example_url" id="wo_example_url_label">Example URL:</label>';
								if(isset($_GET['defect_id'])) {
								echo '<input name="wo_example_url" id="wo_example_url" class="field_large" type="text" value="' . htmlspecialchars(@$wo_data[0]['example_url']) .'" />';
								echo '<input type="checkbox" '.((!empty($example_url_cookie)) ? 'checked' : '').' title="Remember me" name="qa_exampleurl_remember" id="qa_exampleurl_remember" style="display:none;" />';
								}else{
								echo '<input name="wo_example_url" id="wo_example_url" class="field_large" type="text" value="'.$example_url_cookie.'" />';
								echo '<input type="checkbox" '.((!empty($example_url_cookie)) ? 'checked' : '').' title="Remember me" name="qa_exampleurl_remember" id="qa_exampleurl_remember" />';
								}
								
							echo '</li>
							<li>
								<label for="wo_desc" id="wo_desc_label"><img style="float:left;margin-left:40px;margin-top:-4px;" src="/_images/tool-tip-lighthouse-v2.png" onmouseout="hideTooltip();" onmouseover="showTooltip();" >Description:</label><div style="display: none;" class="wo_comment" id="qa_tooltip"><div class="wo_comment_header"></div><div class="wo_comment_content"><p class="risk_desc">
<b>Description should include the following:</b>
<br><br>
<b>-</b> Further detailed explanation of the summary already provided<br>
<b>-</b> Steps to Recreate:  Explain each step correctly in sequence and clearly<br>
<b>-</b> Expected Result:  Illustrate the expected behavior of the application<br>
<b>-</b> Actual Result:  Document what actually happened in the application<br>
<b>-</b> Reproducible - Is the issue reproducible - Yes or No?<br>
<b>-</b> Environment - (Prod, Stage, QA, Dev)<br>

</p></div><div class="wo_comment_footer"></div></div>
								<textarea name="wo_desc" id="wo_desc" class="field_large">' .htmlentities(@$wo_data[0]['body'],ENT_NOQUOTES,'UTF-8') .'</textarea>
							</li>

							<li>
								<label for="wo_attachment" id="dirName_label"><br><br>Attachments:</label>
								<div class="qa_no_label_side">
									<div class="wo_dimmer" id="file_upload_dimmer" style="display: none;"></div>
									<form id="file_upload_form" name="file_upload_form" accept-charset="utf-8" method="post" action="" enctype="multipart/form-data">
									<input type="hidden" name="defect_id" id="defect_id" value="' .@$defect_id .'" />
									<input type="hidden" name="dirName" id="dirName" value="' .@$defect_id .'" />
									
									<label for="upload_file" id="upload_file_label">Attach files (each file should be under 10MB)</label>
									<ul class="attached" id="file_upload_list">';
									
										if(isset($_GET['defect_id'])) {
											$file_data = QaDisplay::getQuery("SELECT * FROM `qa_files` WHERE `defect_id`='" .$wo_data[0]['id'] ."'");
											
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
										<input class="field_xlarge" onChange="checkDirName(file_upload_form);" type="file" name="upload_file" id="upload_file" />
										<img src="/_images/ajax-loader.gif" alt="ajax-loader" id="file_upload_ajax" style="display: none;">
									</div>
									<iframe name="upload_target" src="/_ajaxphp/uploadFile.php" width="400" height="100" style="display:none;"> </iframe>
									
								</div>'
								.QaDisplay::assignDateHTML(@$wo_data[0]['id'])
								.'</li>

							';
							
						echo'</ul><div '.$pageLoadHide.' id="pageLoadHide"> <ul>										
							
							
						</ul>
							</div>

								<!--=========== COLUMN BREAK ===========-->
					<div class="qaworkorder_content_actions ' . $wo_archive_status . '">';
					if(!empty($_GET['defect_id']))
					{
						echo '<button onClick="saveWorkOrder(\'update\'); return false;"><span>update</span></button>';
					}
					else
					{
						echo '<button id="wo_save" onClick="saveWorkOrder(\'save\'); return false;"><span>Save</span></button>';
						echo '<button id="wo_save" onClick="saveWorkOrder(\'save&add\'); return false;"><span>Save & Add new</span></button>';
					}			

					if(!empty($_GET['defect_id']) && $wo_data[0]['status'] != '8')
					{
						echo ' <button onClick="$(\'.message_close\').css({display:\'block\'}); return false;"><span>close defect</span></button>';
					}
					else if($wo_data[0]['status'] == '8')
					{
						echo ' <button onClick="saveWorkOrder(\'reopen\'); return false;"><span>reopen defect</span></button>';
					}
					else if(empty($_GET['defect_id']))
					{
						echo ' <button onClick="$(\'.message_clear\').css({display:\'block\'}); return false;"><span>clear </span></button>';
					}
			
					echo '<div class="wo_dimmer" id="save_buttons_dimmer"></div>
						<button onClick="$(\'.message_cancel\').css({display:\'block\'}); return false;"><span>cancel</span></button>';

					if(!empty($_GET['defect_id'])){
						echo ' <button onClick="$(\'.message_workorder_audit\').css({display:\'block\'}); return false;"><span>Quality Audit</span></button>';
					}
					echo '</div>

					<!--=========== COLUMN BREAK ===========-->
					</div>
					<!--=========== COLUMN BREAK ===========-->

					<div class="workorder_content_col2">';
				if(isset($_GET['defect_id'])) {
					
						echo "<input type='hidden' id='qa_assigned_to_user' value='".$wo_data[0]['assigned_to']."'>";
						echo "<input type='hidden' id='qa_current_status' value='".$wo_data[0]['status']."'>";
						echo '<div class="side_bucket_container" >
							<div class="side_bucket_title">Project Management</div>
							<div class="side_bucket_content">
								<img src="'.QaDisplay::userIMG(@$wo_data[0]['assigned_to']).'" id="assigned_user_img" class="requestor_photo">
								<ul class="qa_project_management">
									<li>
										<label id="qa_status_label" for="wo_status">Status:</label>';										
										echo '<select class="field_small" onchange="qaStatusChange(this.value);" name="wo_status" id="wo_status">';												
												if(isset($_GET['defect_id'])) {
													echo QaDisplay::getStatusOptionEditHTML($wo_data[0]['status']);
												} else {
													echo QaDisplay::getStatusOptionEditHTML();
												}												
										echo '</select>
									</li>';	

								echo '<li>
									<label for="wo_assigned_user" id="wo_assigned_user_label" >Assigned To:</label>
									<select class="field_small" name="wo_assigned_user" id="wo_assigned_user" onChange="changeImage(this.value);">';
										echo QaDisplay::getUserAssignOptionEditHTML($wo_data[0]['assigned_to']);
									echo '</select>
								<input type="checkbox" '.((!empty($assigned_to_cookie)) ? 'checked' : '').' style="display:none" name="qa_assigned_remember" id="qa_assigned_remember" title="Remember me" />
									</li>';
									
								if(isset($_GET['defect_id'])) {
										
										if(@$wo_data[0]['completed_date'] != "") {
											$esti_date = @date("m/d/Y", mktime(0,0,0,$completed_date_part[1],$completed_date_part[2],$completed_date_part[0]));
										} else {
											$esti_date = '';
										}										
										if(!empty($wo_data[0]['creation_date'])){
											$start_date = strtotime(@$wo_data[0]['creation_date']);
											$start_date  = Date('m/d/Y h:i A', $start_date);
										}
										else{
											$start_date = "";
										}
										if(!empty($wo_data[0]['closed_date'])) {
											$close_date = strtotime(@$wo_data[0]['closed_date']);
											$close_date  = Date('m/d/Y h:i A', $close_date);
										}
										else{
											$close_date = "";
										}
									} else {
										$start_date = '';
										$esti_date = '';
										$close_date = '';
									}
								$last_update_date="";
								foreach($workorder_audit as $row )
								{				
									if($row['audit_id']!='1')
									{
										$last_update_date = strtotime($row['log_date']);
									    $last_update_date  = Date('m/d/Y h:i A', $last_update_date);
									}
								}
									
									echo '<li>
											<label for="start_date">Opened Date:</label>
											<input name="start_date" id="start_date" style="width:170px;" readonly="readonly" class="readonly" type="text" value="' .$start_date .'">
										 </li>
										<li>
											<label for="last_modified_date">Last Modified :</label>
											<input name="last_modified_date" style="width:170px;" id="last_modified_date" value="' .$last_update_date .'" readonly="readonly" class="readonly" type="text">
										</li>

									<li><label for="close_date">Close Date:</label>
									<input name="close_date" id="close_date" style="width:170px;" value="' .$close_date .'" readonly="readonly" class="readonly" type="text"></li>

								</ul>
								<div class="clearer"></div>
							</div>
						</div>';
						// Project Management Ends
						}
						else
						{
								echo '<input type="hidden" name="wo_status" id="wo_status" value="1" />
								<input name="start_date" type="hidden" id="start_date" value="">';
						}
						echo '<div class="side_bucket_container" >

							<div class="side_bucket_title">Other Information</div>
							<div class="side_bucket_content">
								<ul class="qa_other_fields">';
								if(isset($_GET['defect_id'])) {
									echo '<li>
										<label for="wo_project" id="wo_project_label">Project:</label>
										<select class="field_medium" style="width:200px" name="wo_project" id="wo_project" onchange="updateVersion(this.value);">';
										echo QaDisplay::getProjectAllOptionEditHTML($wo_data[0]['project_id'],'1');									
										echo '</select>
										<input type=checkbox '.((!empty($pj_cookie)) ? 'checked' : '').' name="qa_project_remember" id="qa_project_remember" style="display:none;" title="Remember me"/>
									</li>';
									}
									echo '<li>
									<label for="QA_ORIGIN" id="QA_ORIGIN_label">Origin :</label>
									<select class="field_medium" name="QA_ORIGIN" id="QA_ORIGIN" >
									<option value="_blank">--Select Origin--</option>';
									if(isset($_GET['defect_id'])) {
										echo QaDisplay::getcustomDropDown("QA_ORIGIN",$wo_data[0]['origin']);
										echo '</select>
										<input type=checkbox '.((!empty($origin_cookie)) ? 'checked' : '').' name="qa_origin_remember" id="qa_origin_remember" style="display:none;" title="Remember me"/>';
									}
									else
									{
										echo QaDisplay::getcustomDropDown("QA_ORIGIN",$origin_cookie);
										echo '</select>
										<input type=checkbox '.((!empty($origin_cookie)) ? 'checked' : '').' name="qa_origin_remember" id="qa_origin_remember" title="Remember me"/>';
									}



									echo '</li>
									<li>
									<label for="QA_OS" id="QA_OS_label">OS :</label>

									<select class="field_medium" name="QA_OS" id="QA_OS" >
									<option value="_blank">  --Select OS--  </option>';
									if(isset($_GET['defect_id'])) {
										echo QaDisplay::getcustomDropDown("QA_OS",$wo_data[0]['os']);
										echo '</select>
										<input type=checkbox '.((!empty($os_cookie)) ? 'checked' : '').' name="qa_os_remember" id="qa_os_remember" style="display:none;" title="Remember me"/>';
									}
									else
									{
										echo QaDisplay::getcustomDropDown("QA_OS",$os_cookie);
										echo '</select>
										<input type=checkbox '.((!empty($os_cookie)) ? 'checked' : '').' name="qa_os_remember" id="qa_os_remember" title="Remember me" />';
									}

									echo '</li>
									<li>
									<label for="QA_BROWSER" id="QA_BROWSER_label">Browser :</label>

									<select class="field_medium" name="QA_BROWSER" id="QA_BROWSER" >
									<option value="_blank">--Select Browser--</option>';
									if(isset($_GET['defect_id'])) {
										echo QaDisplay::getcustomDropDown("QA_BROWSER",$wo_data[0]['browser']);
										echo '</select>
										<input type=checkbox '.((!empty($browser_cookie)) ? 'checked' : '').' name="qa_browser_remember" id="qa_browser_remember" style="display:none;" title="Remember me" />';
									}
									else
									{
										echo QaDisplay::getcustomDropDown("QA_BROWSER",$browser_cookie);
										echo '</select>
										<input type=checkbox '.((!empty($browser_cookie)) ? 'checked' : '').' name="qa_browser_remember" id="qa_browser_remember" title="Remember me" />';
									}

									echo '</li>

									<li>
									<label for="QA_VERSION" id="QA_VERSION_label">Version :</label>

									<select class="field_medium" name="QA_VERSION" id="QA_VERSION" >
									<option value="_blank">--Select Version--</option>';
									if(isset($_GET['defect_id'])) {
										echo QaDisplay::getProjectVersionDropDown($wo_data[0]['project_id'],$wo_data[0]['version']);
										echo '</select>
										<input type=checkbox '.((!empty($version_cookie)) ? 'checked' : '').' name="qa_version_remember" id="qa_version_remember" style="display:none;" title="Remember me"/>';
									}
									else
									{
										echo QaDisplay::getProjectVersionDropDown($pj_cookie,$version_cookie);
										echo '</select>
										<input type=checkbox '.((!empty($version_cookie)) ? 'checked' : '').' name="qa_version_remember" id="qa_version_remember" title="Remember me" />';
									}
									//28522
									echo '</li>

									<li>
									<label for="QA_ITERATION" id="QA_ITERATION_label">Iteration :</label>

									<select class="field_medium" name="QA_ITERATION" id="QA_ITERATION" >
									<option value="_blank">--Select Iteration--</option>';
									if(isset($_GET['defect_id'])) {
											echo QaDisplay::getProjectIterationDropDown($wo_data[0]['project_id'],$wo_data[0]['iteration']);
										echo '</select>
										<input type=checkbox '.((!empty($iteration_cookie)) ? 'checked' : '').' name="qa_iteration_remember" id="qa_iteration_remember" style="display:none;" title="Remember me"/>';
									}
									else
									{
										echo QaDisplay::getProjectIterationDropDown($pj_cookie,$iteration_cookie);
										echo '</select>
										<input type=checkbox '.((!empty($iteration_cookie)) ? 'checked' : '').' name="qa_iteration_remember" id="qa_iteration_remember" title="Remember me" />';
									}
									echo '</li>

									<li>
									<label for="QA_PRODUCT" id="QA_PRODUCT_label">Product :</label>

									<select class="field_medium" name="QA_PRODUCT" id="QA_PRODUCT" >
									<option value="_blank">--Select Product--</option>';
									if(isset($_GET['defect_id'])) {
										echo QaDisplay::getProjectProductDropDown($wo_data[0]['project_id'],$wo_data[0]['product']);
										echo '</select>
										<input type=checkbox '.((!empty($product_cookie)) ? 'checked' : '').' name="qa_product_remember" id="qa_product_remember" style="display:none;" title="Remember me"/>';
									}
									else
									{
										echo QaDisplay::getProjectProductDropDown($pj_cookie,$product_cookie);
										echo '</select>
										<input type=checkbox '.((!empty($product_cookie)) ? 'checked' : '').' name="qa_product_remember" id="qa_product_remember" title="Remember me" />';
									}
									//END LH#28522
									echo '</li>
									<li>
									<label for="QA_DETECTED_BY" id="QA_DETECTED_BY_label">Detected By:</label>';

									if(isset($_GET['defect_id'])) {
										echo '<input name="QA_DETECTED_BY" id="QA_DETECTED_BY" style="width:194px;" value="'.QaDisplay::fetchUserName($wo_data[0]['detected_by']).'" readonly="readonly" class="readonly" type="text">';
									} else {
										echo '<select class="field_medium" name="QA_DETECTED_BY" id="QA_DETECTED_BY" >';
										echo QaDisplay::getUserOptionHTML();
										echo '</select>';
									}
								echo '</li>';
							if(isset($_GET['defect_id'])) {
								echo '<li >';
								$submittedBy = QaDisplay::fetchUserName($wo_data[0]['requested_by']);
							}else
							{
								echo '<li style="display:none;">'; 
								$submittedBy = $_SESSION['user_id'];
							}
							echo '<label for="wo_requested_by" id="wo_requested_by_label">Submitted By:</label>';
							echo '<input name="wo_requested_by" id="wo_requested_by" style="width:194px;" value="'.$submittedBy.'" readonly="readonly" class="readonly" type="text">
							</li>';							

							echo '</ul>
								<div class="clearer"></div>
							</div>
						</div>
					</div>
				
				</div>
				<div class="clearer"></div>
				<!--==| END: Bucket |==-->
			
				<!--==| START: Bucket |==-->
				<div class="content">';
					if(!isset($_GET['defect_id'])) {
						echo '<div class="wo_dimmer" id="comment_dimmer"></div>';
					}			
				
					echo '<!--=========== COLUMN BREAK ===========-->
					<div class="workorder_content_col3">
						<div class="main_bucket_container">
							<div class="main_bucket_title"><h4 id="number_of_comments">Comments (0)</h4></div>
							<div class="main_bucket_content">
							<ul class="comment_field_container">
									<li><label for="comment">New Comment:</label><textarea name="comment" id="comment" class="field_large ' . $wo_archive_text . '" ' . $wo_archive_text . '></textarea></li>
								</ul>
								<div class="new_comment_actions ' . $wo_archive_status . '"><button class="secondary" onClick="submitComment(); return false;"><span>Submit Comment</span></button></div>
								<ul class="comments" id="comments_list">';
									
									if(isset($_GET['defect_id'])) {
										$comment_data = QaDisplay::getQuery("SELECT * FROM `qa_comments` WHERE `defect_id`='" .$wo_data[0]['id'] ."' order by date desc");
										$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
										for($cx = 0; $cx < sizeof($comment_data); $cx++) {
											$text=$comment_data[$cx]["comment"];
											$text_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($text,ENT_NOQUOTES, 'UTF-8'));
											$text_string=nl2br($text_string);
											$comment_date_time_part = explode(" ", $comment_data[$cx]['date']);
											$comment_date_part = explode("-", $comment_date_time_part[0]);
											$comment_time_part = explode(":", $comment_date_time_part[1]);											
											$comment_user_data = QaDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$comment_data[$cx]['user_id'] ."' LIMIT 1");
											echo '<li>
												<img src="'.$comment_user_data[0]['user_img'].'" class="comment_photo" />
												<div class="comment_body">
													<p><strong>' .ucfirst($comment_user_data[0]['first_name']) .' ' .ucfirst($comment_user_data[0]['last_name']) .'</strong><br>
													<em>' .@date("D M j \a\\t g:i a", mktime(@$comment_time_part[0],@$comment_time_part[1],@$comment_time_part[2],@$comment_date_part[1],@$comment_date_part[2],@$comment_date_part[0])) .'</em></p>

													<p>'.$text_string.'</p>
												</div>
											</li>';
										}
									}
									
								echo '</ul>
								

								
								<div class="clearer"></div>
							</div>
						</div>
					</div>
					<!--=========== COLUMN BREAK ===========-->
					<div class="workorder_content_col4">
						<div class="side_bucket_container bucket_container_last">

							<div class="side_bucket_title">CC List</div>
							<div class="side_bucket_content">';
								if(isset($_GET['defect_id'])) {
									echo QaDisplay::getCCList($_GET['defect_id']);
								} else {
									echo '<input type="hidden" name="cclist" id="cclist" value="" />';
								}
								echo '<ul id="cc_list">';
									if(isset($_GET['defect_id'])) {
										$cclist = explode(",", $wo_data[0]['cclist']);
										for($lstx = 0; $lstx < sizeof($cclist); $lstx++) {
											if(!empty($cclist[$lstx])) {
												$cc_user_data = QaDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$cclist[$lstx] ."'");
												
												echo '<li><div class="cclist_name">'.ucfirst($cc_user_data[0]['first_name']) .' ' .$cc_user_data[0]['last_name']
													.'</div>
													<button class="status cclist_remover ' . $wo_archive_status . '" onclick="removeCcUser('
														.$cclist[$lstx]
													.'); return false;"><span>remove</span></button>
												</li>';
											}
										}
									}
								echo '</ul>
								<div class="cclist_actions ' . $wo_archive_status . '" id="add_cc">
									<button class="secondary" onclick="$(\'#add_cc\').css({display:\'none\'});$(\'#select_cc\').css({display:\'block\'}); return false;"><span>+ Add Person to CC List</span></button>
								</div>
								<div class="cclist_actions" id="select_cc" style="display: none;">
									<select name="cc_user" id="cc_user" style="margin-bottom:2px;">';
										if(isset($_GET['defect_id'])) {
											echo QaDisplay::getUserOptionEditHTML();
										} else {
											echo QaDisplay::getUserOptionHTML();
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
				<div id="new_comment_notification" style="display:none;">
					<div id="sticky">
						<a class="ui-notify-close ui-notify-cross" href="#">x</a>
						<h1>#{title}</h1>
						<p>#{text}</p>
					</div>
				</div>
				<div class="clearer"></div>
				<script>
					$(function() {
                        $("input[name=time_sensitive]").attr("checked", true);
                        $(".date_picker").datepicker({ 
							showOn: "both",
							buttonImage: "/_images/date_picker_trigger.gif", 
							buttonImageOnly: true 
						});
                    });
				</script>
				
				<!--==| END: Bucket |==-->
				<div class=" message_required message_clear message_cancel message_required message_close message_workorder_audit message_reopen" style="width:3000px;height:1024px;position:fixed;background-color:#fffff;z-index:1.0;margin-top:-500px;margin-left:-630px;opacity: 0.3;filter:alpha(opacity=30);zoom:1.0;"></div>
				
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
					You are about to leave this defect. Any unsaved information will be lost.<br />Do you want to continue?
				</p>
				<input type="hidden" name="archive_project_confirm" id="archive_project_confirm" value="" />
				
				<div style="clear: both;"></div>
				
				<div class="duplicate_buttons">
					<button onClick="document.location = \'/quality/\'; return false;"><span>Yes</span></button> 
					<button class="cancel" onClick="$(\'.message_cancel\').css({display:\'none\'}); return false;"><span>No</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>


			<div class="message_workorder_audit">
				<h3>
					Quality Defect Audit</h3><br><p>';

				$audit_user_list = array();
				$audit_status = array();
				foreach($workorder_audit as $row )
				{
					$str_date = strtotime($row['log_date']);
					if(empty($audit_user_list[$row['log_user_id']]))
					{
						$audit_user_data = QaDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$row['log_user_id'] ."' LIMIT 1");
						$audit_user_list[$row['log_user_id']] = $audit_user_data[0]['first_name'] ." " .@$audit_user_data[0]['last_name'];
					}

					if(empty($audit_user_list[$row['assign_user_id']]))
					{
						$audit_user_data = QaDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$row['assign_user_id'] ."' LIMIT 1");
						$audit_user_list[$row['assign_user_id']] = $audit_user_data[0]['first_name'] ." " .@$audit_user_data[0]['last_name'];
					}

					if(empty($audit_status[$row['status']]))
					{
						$audit_status_data = QaDisplay::getQuery("SELECT * FROM `lnk_qa_status_types` WHERE `id`='" .$row['status'] ."' LIMIT 1");
						$audit_status[$row['status']] = $audit_status_data[0]['name'] ;
					}

					if($row['audit_id']=='1')
					{
						echo "- New Defect created by ".$audit_user_list[$row['log_user_id']]." on ".Date('m/d/Y h:i A', $str_date)."<br>";
					}
					else if($row['audit_id']=='2')
					{
						echo "- "."Defect ".$row['name']." ".$audit_user_list[$row['assign_user_id']]." on ".Date('m/d/Y h:i A', $str_date). " status is ".$audit_status[$row['status']]."<br>";
					}
					else if($row['audit_id']=='3')
					{
						echo "- "."Defect ".$row['name']." to ".$audit_status[$row['status']]." by ".$audit_user_list[$row['log_user_id']]." on ".Date('m/d/Y h:i A', $str_date)."<br>";
					}
					else if($row['audit_id']=='4')
					{
						echo "- ".$row['name']." by ".$audit_user_list[$row['log_user_id']]." on ".Date('m/d/Y h:i A', $str_date)."<br>";
					}
				}
				echo '</p>
				<input type="hidden" name="archive_project_confirm" id="archive_project_confirm" value="" />
				
				<div style="clear: both;"></div>
				
				<div class="duplicate_buttons">			
					<button class="cancel" onClick="$(\'.message_workorder_audit\').css({display:\'none\'}); return false;"><span>Close</span></button>
					<div style="clear: both;"></div>
				</div>
			</div>

			<div class="message_close">				

				<p>
					You are about to close this defect.<br />Do you want to continue?
				</p>				
				<div style="clear: both;"></div>
				
				<div class="duplicate_buttons">
					<button onClick="closeDefect(); return false;"><span>Yes</span></button> 
					<button class="cancel" onClick="$(\'.message_close\').css({display:\'none\'}); return false;"><span>No</span></button>
					<div style="clear: both;"></div>
				</div>

			</div>

			<div class="message_clear">			
				<p>
					You are about to clear this defect. Any unsaved information will be lost.<br />Do you want to continue?
				</p>
				<input type="hidden" name="archive_project_confirm" id="archive_project_confirm" value="" />
				
				<div style="clear: both;"></div>
				
				<div class="duplicate_buttons">
					<button onClick="document.location = \'/quality/index/create/\'; return false;"><span>Yes</span></button> 
					<button class="cancel" onClick="$(\'.message_clear\').css({display:\'none\'}); return false;"><span>No</span></button>
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
			$defect_id = ($_GET['defect_id']);
			$user_id = $_SESSION["user_id"];
			$save = "";
			if(array_key_exists("save_type", $_POST)){
				$save = $_POST["save_type"];
			}
			switch($save){
				case "save_wo":
						$new_status = $_POST["wo_status"];
						$new_assigned = $_POST["wo_assigned_user"];

						$old_wo_data = QaDisplay::getQuery("SELECT * FROM `workorders` WHERE `id`='$defect_id' LIMIT 1");
						$assigned_date = "";
						if($old_wo_data[0]['assigned_to'] != $new_assigned){
							$assigned_date = ", `assigned_date`=NOW() ";
							$new_status = "2";
						}
						$update_wo_sql = "UPDATE `workorders` SET `assigned_to`='$new_assigned', `status`='$new_status' $assigned_date WHERE `id`='$defect_id'";
						$sql = $update_wo_sql;
						QaDisplay::executeQuery($update_wo_sql);
						
						if($new_status == "2"){
							// When a wo is assigned to a new person.
							$this->sendEmail("assigned", $defect_id, $user_id);
						}else{
							//change in status
							$this->sendEmail("status_change", $defect_id, $user_id);
						}
						break;
				case "close_wo":
						$close_wo_sql = "UPDATE `workorders` SET `closed_date`=NOW(), `status`='1' WHERE `id`='$defect_id'";
						$sql = $close_wo_sql;
						QaDisplay::executeQuery($close_wo_sql);
						$this->sendEmail("status_change", $defect_id, $user_id);
						break;
				case "comment_wo":
						$new_comment = $_POST["comment"];
						$insert_wo_comment = "INSERT INTO `workorder_comments` (`workorder_id`,`user_id`,`comment`,`date`) VALUES ('$defect_id','$user_id','$new_comment',NOW())";
						$sql = $insert_wo_comment;
						QaDisplay::executeQuery($insert_wo_comment);
						$this->sendEmail("comment", $defect_id, $user_id, $new_comment);
						break;
				case "":
						$sql = "No case matching.";
						break;
			}

			$wo_data = QaDisplay::getQuery("SELECT * FROM `workorders` WHERE `id`='$defect_id' LIMIT 1");
			
			$requestors_data = QaDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" . $wo_data[0]['requested_by'] ."' LIMIT 1");
			$project_data = QaDisplay::getQuery("SELECT CONCAT(`project_code`, ' - ', `project_name`) AS name FROM `projects` WHERE `id`='" . $wo_data[0]['project_id'] . "' LIMIT 1");
			$priority_data = QaDisplay::getQuery("SELECT CONCAT(`name`, ' - ', `time`) AS priority FROM `lnk_workorder_priority_types` WHERE `id`='" . $wo_data[0]['priority'] . "' LIMIT 1");

			if(@$wo_data[0]['creation_date'] != "") {
				$start_date_time_part = explode(" ", @$wo_data[0]['creation_date']);
				$start_date_part = explode("-", @$start_date_time_part[0]);
				$start_date = @date("m/d/Y", mktime(0,0,0,$start_date_part[1],$start_date_part[2],$start_date_part[0]));
			} else {
				$start_date = '';
			}
			if(@$wo_data[0]['completed_date'] != "") {
				$completed_date_time_part = explode(" ", @$wo_data[0]['completed_date']);
				$completed_date_part = explode("-", @$completed_date_time_part[0]);
				$esti_date = @date("m/d/Y", mktime(0,0,0,$completed_date_part[1],$completed_date_part[2],$completed_date_part[0]));
			} else {
				$esti_date = '';
			}
			if(@$wo_data[0]['closed_date'] != "") {
				$closed_date_time_part = explode(" ", @$wo_data[0]['closed_date']);
				$closed_date_part = explode("-", @$closed_date_time_part[0]);
				$close_date = @date("m/d/Y", mktime(0,0,0,$closed_date_part[1],$closed_date_part[2],$closed_date_part[0]));
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

			$comment_data = QaDisplay::getQuery("SELECT * FROM `qa_comments` WHERE `defect_id`='" . $defect_id ."' order by date");
										
			for($cx = 0; $cx < sizeof($comment_data); $cx++) {
				$comment_date_time_part = explode(" ", $comment_data[$cx]['date']);
				$comment_date_part = explode("-", $comment_date_time_part[0]);
				$comment_time_part = explode(":", $comment_date_time_part[1]);											
				$comment_user_data = QaDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$comment_data[$cx]['user_id'] ."' LIMIT 1");

				$comment[$cx]['name'] = ucfirst($comment_user_data[0]['first_name']) .' ' .ucfirst($comment_user_data[0]['last_name']);
				$comment[$cx]['timestamp'] = @date("D M j \a\\t g:i a", mktime(@$comment_time_part[0],@$comment_time_part[1],@$comment_time_part[2],@$comment_date_part[1],@$comment_date_part[2],@$comment_date_part[0]));
				$comment[$cx]['comment_text'] = nl2br(htmlentities($comment_data[$cx]['comment'],ENT_NOQUOTES, 'UTF-8'));
			}

			$this->view->comment = $comment;
			$this->view->defect_id = $defect_id;
			$this->view->vars = $vars;
			$this->view->status = QaDisplay::getStatusOptionEditHTML($vars['status'], $vars['project_id']);
			$this->view->assigned = QaDisplay::getUserAssignOptionEditHTML($vars['assigned_user']);
//			$this->render("edit");
		}

		public function sendEmail($type, $defect_id, $userId, $comment_text=''){

			$select_email_users = "SELECT * FROM `workorders` WHERE `id`='$defect_id' LIMIT 1";
			$email_res = QaDisplay::getQuery($select_email_users);
			if(sizeOf($email_res) > 0) {
				$new_commenter = "SELECT * FROM `users` WHERE `id`='$userId' LIMIT 1";
				$commenter_res = QaDisplay::getQuery($new_commenter);
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

				$bc_id_query = "SELECT  `bcid`, `project_id`, `title`, `priority` FROM `workorders` WHERE `id`='" . $defect_id ."' LIMIT 1";
				$bc_id_result = QaDisplay::getQuery($bc_id_query);
				$bc_id_row = $bc_id_result[0];
				
				$select_priority = "SELECT * FROM `lnk_workorder_priority_types` WHERE `id`='" . $bc_id_row['priority'] ."'";
				$pri_res = QaDisplay::getQuery($select_priority);
				$pri_row = $pri_res[0];

				$select_project = "SELECT * FROM `projects` WHERE `id`='" . $bc_id_row['project_id'] ."'";
				$project_res = QaDisplay::getQuery($select_project);
				$project_row = $project_res[0];

				$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
				$company_res = QaDisplay::getQuery($select_company);
				$company_row = $company_res[0];

				$subject = "WO: " . $bc_id_row['title'] . " - Lighthouse Work Order Message";
				$headers = 'From: '.WO_EMAIL_FROM.'';

				switch($type){
					case 'assigned':
							$sendList = array($email_row['assigned_to'], $email_row['requested_by']);
							$file_list = "";
							$select_file = "SELECT * FROM `workorder_files` WHERE workorder_id='" . $defect_id . "' order by upload_date desc";
							$file_res = QaDisplay::getQuery($select_file);
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
								$email_addr_res = QaDisplay::getQuery($select_email_addr);
								$email_addr_row = $email_addr_res[0];
								if($user == $email_row['assigned_to']){
									$assignedTo = $user_row['email'];
								}
								$to = $email_addr_row['email'];
								$msg =  "Company: " . $company_row['name'] . "\r\n"
										."Project: " . $project_row['project_code'] ." - " . $project_row['project_name'] ."\r\n"
										."Link: " .BASE_URL ."/quality/index/edit/?defect_id=" . $defect_id  ."\r\n\r\n"
										."WO [#" . $defect_id . "] has been assigned to " . $assignedTo . "\r\n\r\n"
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
									$email_addr_res = QaDisplay::getQuery($select_email_addr);
									$email_addr_row = $email_addr_res[0];
									$to = $email_addr_row['email'];
									$msg =  "Company: " . $company_row['name'] . "\r\n"
											."Project: " . $project_row['project_code'] ." - " . $project_row['project_name'] ."\r\n"
											."Link: " .BASE_URL ."/quality/index/edit/?defect_id=" . $defect_id  ."\r\n\r\n"
											."WO [#" . $defect_id . "] has been " . $woStatusText . " by " . $_SESSION['first'] . " ". $_SESSION['last'] . "\r\n\r\n"
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
								$email_addr_res = QaDisplay::getQuery($select_email_addr);
								$email_addr_row = $email_addr_res[0];
								
								$to = $email_addr_row['email'];
								
								$msg = "Company: " . $company_row['name'] . "\r\n"
										."Project: " .$project_row['project_code'] ." - " .$project_row['project_name'] ."\r\n"
										."Link: " .BASE_URL ."/quality/index/edit/?defect_id=" . $defect_id  ."\r\n\r\n"
										.ucfirst($commenter_row['first_name']) ." " .ucfirst($commenter_row['last_name']) ." commented on work order [#" . $defect_id . "]\r\n\r\n"
										."\t- Priority: " .$pri_row['name'] ."-" .$pri_row['time'] ."\r\n"
										."\t- Comment: " . htmlentities($comment_text) ."\r\n\r\n"
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
	}
	
?>
