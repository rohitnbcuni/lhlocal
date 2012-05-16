//Quick Filter Sort
var qualityList = Array();
var currentSort;
var sortDir=0;
var archive_currentSort;
var archive_sortDir=0;
var aOpen = false;
var selectedRow;
var aSelectedRow;
var projDetails; 
var privacyLookup = Array();
var statusLookup = Array();
var allProjectList;
var allAssignedList;
var isOnPageload = true;
var ajaxRequest;  // The variable that makes Ajax possible!

$(document).ready(function() {
	privacyLookup[0] = "Low";
	privacyLookup[1] = "Medium";
	privacyLookup[2] = "High";
	
	statusLookup[0] = "Open";
	statusLookup[1] = "Assigned";
	statusLookup[2] = "Closed";
	$('#wo_dimmer_ajax').css({display:'block'});

	jQuery.getJSON('/_ajaxphp/quality_json.php', function(json) {
		qualityList = json;
		cookie_date = getCookie("lighthouse_qa_data");
		var lh_qa_project_cookie = getCookie("lh_qa_project_cookie");

		$("#wo_containter .title_small").css({display:"none"});
		$("#wo_containter .quality_rows").css({display:"none"});		
		loadAllProjectList();
		loadAllAssignedList();
		// If the cookie is present(with previous selection), then load that filtered list sorted with ID descending.
		if(cookie_date != ""){
		if(lh_qa_project_cookie!='')
		{
			$("#project_filter").val(lh_qa_project_cookie);
			if(cookie_date!=null)
			{
				if(cookie_date!='')
				{
					data = cookie_date.split('~');
					$("#status_filter").val(data[2]);
					$("#assigned_filter").val(data[3]);
					$("#severity_filter").val(data[4]);					
				}
			}
		}
		else
		{			
			data = cookie_date.split('~');

			$("#client_filter").val(data[0]);
			if(data[0] != "-1"){
				loadProjectList();
			}


			$("#project_filter").val(data[1]);
			$("#status_filter").val(data[2]);
			$("#assigned_filter").val(data[3]);
			$("#severity_filter").val(data[4]);			
		}
			
			if(getCookie("selectedSortOption")==""){
				sortDir = 1;
        sortQuality("id");
				}else{
				previousSortSelection = getCookie("selectedSortOption");
				previousSortSelection = previousSortSelection.split(":");
				sortDir = previousSortSelection[1];
				sortQuality(previousSortSelection[0]);
			}

		}else{
		//////////////////////changes for 18593
		var cookie_filter;
        	cookie_filter = getCookie("mycookie");
		if(cookie_filter==''){		
		$("#status_filter").val(99);
		Set_Cookie( 'mycookie', '1', 7, '/', '', '' );
		}
		//////////////////////////////////
         if(getCookie("selectedSortOption")==""){
      sortDir = 1;   
			sortQuality("title");
		}else{
			previousSortSelection = getCookie("selectedSortOption");
			previousSortSelection = previousSortSelection.split(":");
			sortDir = previousSortSelection[1];
			sortQuality(previousSortSelection[0]);
		}
          
		}
		$("#wo_containter .title_small").css({display:"block"});
		$("#wo_containter .workorders_rows").css({display:"block"});
	//	$('#wo_dimmer_ajax').css({display:'none'});
	});

});

function getWO_On_Status(){
	typeFilter = document.getElementById("project_status_filter").value;
	if(typeFilter == 1)
	{	
		$('#archiveBTN').html('<span>archive</span>');
		$('#archiveBTN').unbind('click');
		$('#archiveBTN').attr('onclick','');
		$('#archiveBTN').bind('click', function() {
			 archiveWO_CheckList();
		});
	}
	else
	{
		$('#archiveBTN').html('<span>Unarchive</span>');
		$('#archiveBTN').unbind('click');
		$('#archiveBTN').attr('onclick','');		
		$('#archiveBTN').bind('click', function() {
			 unarchiveWO_CheckList();
		});
	}
	$('#wo_dimmer_ajax').css({display:'block'});
	
	jQuery.getJSON('/_ajaxphp/workorder_json.php?status='+typeFilter, function(json) {
		qualityList = json;
		sortDir = 1;
		sortQuality("title");
		loadAllProjectList();
		loadProjectList();
		loadAllAssignedList();
		$('#wo_dimmer_ajax').css({display:'none'});
	});
}
function qaShowSeverity(theId,theValue) {
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/qa_custom_feild_list.php?feildKEY=QA_SEVERITY&feildValue="+theValue,
			success: function(msg) {
				if($('#severity_select_'+theId).html() == "") {
					$('#severity_select_'+theId).html(msg);
					$('#severity_select_'+theId).bind('blur', function(){
						$('#severity_'+theId).css({display:'block'});
						$('#severity_select_'+theId).css({display:'none'});
					});
				}
				$('#severity_'+theId).css({display:'none'});
				$('#severity_select_'+theId).css({display:'block'});
				$('#severity_select_'+theId).focus();
			}
		});
}

function qaShowCategory(theId,theValue) {
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/qa_custom_feild_list.php?feildKEY=QA_CATEGORY&feildValue="+theValue,
			success: function(msg) {
				if($('#category_select_'+theId).html() == "") {
					$('#category_select_'+theId).html(msg);
					$('#category_'+theId).css({display:'block'});
					$('#category_select_'+theId).css({display:'none'});

					$('#category_select_'+theId).bind('blur', function(){
						$('#category_'+theId).css({display:'block'});
						$('#category_select_'+theId).css({display:'none'});
					});

				}
				$('#category_'+theId).css({display:'none'});
				$('#category_select_'+theId).css({display:'block'});
				$('#category_select_'+theId).focus();
			}
		});
}


function changeStatus(selectVal, theId) {
	
	var sel = document.getElementById('status_select_'+theId);
	var opts = sel.getElementsByTagName('option');
	var name = "";
	for(var i = 0; i < opts.length; i++) {
		if(opts[i].selected) {
			out = opts[i].text;
		}
	}
	if(selectVal > 0){
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/wo_change_status.php?id="+theId+"&status_id="+selectVal,
			success: function(msg) {
				document.getElementById('status_'+theId).innerHTML = out;
				var oldHTML = $('status_'+theId).html();
				$('#status_'+theId).html(out);
				woHideStatus(theId);
			}
		});
	}
}

function qaShowStatus(theId) {

	assignedto_woid = theId;
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/user_list_last.php?id="+theId,
		success: function(msg) {
			if($('#assigned_select_'+theId).html() == "") {
				$('#assigned_select_'+theId).html(msg);
				$('#assigned_select_'+theId).bind('blur', function(){
					woHideAssigned(theId);
				});
			}
			$('#assigned_a_'+theId).css({display:'none'});
			$('#assigned_select_'+theId).css({display:'block'});
			$('#assigned_select_'+theId).focus();
		}
	});

}

function woHideAssigned(theId) {
	$('#assigned_a_'+theId).css({display:'block'});
	$('#assigned_select_'+theId).css({display:'none'});
}

function changeCategory(selectVal, theId) {
	
	var sel = document.getElementById('category_select_'+theId);

	$.ajax({
		type: "GET",
		url: "/_ajaxphp/qa_change_custom.php?defectId="+theId+"&feildKEY=QA_CATEGORY&feildID="+selectVal,
		success: function(msg) {
			$('#category_'+theId).html(msg);
			$('#category_'+theId).css({display:'block'});
			$('#category_select_'+theId).css({display:'none'});
		}
	});
}

function changeSeverity(selectVal, theId) {
	
	var sel = document.getElementById('severity_select_'+theId);

	$.ajax({
		type: "GET",
		url: "/_ajaxphp/qa_change_custom.php?defectId="+theId+"&feildKEY=QA_SEVERITY&feildID="+selectVal,
		success: function(msg) {
			$('#severity_'+theId).html(msg);
			$('#severity_'+theId).css({display:'block'});
			$('#severity_select_'+theId).css({display:'none'});
		}
	});
}

function archiveAlert(theId) {
	document.getElementById('active_wo').value = theId;
	$('.message_archive').css({display:'block'});
}
function archiveWo(theId) {
	$('.message_archive').css({display:'none'});
	document.getElementById('active_wo').value = "";
	//alert(theId);
	selectedRow = -1;
	var comp = -1;
	var tRemove = -1;
	
	$('#'+theId).css({display:'none'});
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/wo_archive.php?id="+theId,
		success: function(msg) {
			var myArr = theId.split(',');

			for(var i=0;i<myArr.length;i++)
			{
				if(myArr[i]!='')		
					$('#'+myArr[i]).remove();		
			}

		
		}
	});
}
function unarchiveAlert(theId) {
	document.getElementById('active_wo').value = theId;
	$('.message_unarchive').css({display:'block'});
}
function unarchiveWo(theId) {
	$('.message_unarchive').css({display:'none'});
	document.getElementById('active_wo').value = "";
	//alert(theId);
	selectedRow = -1;
	var comp = -1;
	var tRemove = -1;
		
	$('#'+theId).css({display:'none'});
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/wo_unarchive.php?id="+theId,
		success: function(msg) {
			
			var myArr = theId.split(',');

			for(var i=0;i<myArr.length;i++)
			{
				if(myArr[i]!='')		
					$('#'+myArr[i]).remove();		
			}
		}
	});
}
function changeCompany(){
	loadProjectList();
	displayWorkorders();
}

// To Load the project list dynamically with the projects of the selected company
function loadProjectList() {
	clientId = document.getElementById("client_filter").value;
	if(clientId == '-1'){
		html = allProjectList;
	}else{
		html = '<option value="-1">Show All</option>';
		for (var i = 0; i < qualityList.length; i++) {
			if(clientId == "-1" || clientId == qualityList[i]['client']){
				html += '<option value="' + qualityList[i]['project_id'] + '" title="'+ qualityList[i]['project_code'] + ' - ' + qualityList[i]['project_name'] +'">'+ qualityList[i]['project_code'] + ' - ' + qualityList[i]['project_name'] + '</option>';
			}
		}
	}
	$("#project_filter").html(html);
}

// To Load the project list dynamically with all the project for the first time
function loadAllProjectList() {
	clientId = document.getElementById("client_filter").value;
	var allProject = new Array();
	var sortedList = new Array();
	html = '<option value="-1">Show All</option>';
	for (var i = 0; i < qualityList.length; i++) {
		allProject[i] = qualityList[i]['project_code']+'~~'+qualityList[i]['project_name']+'~~'+qualityList[i]['project_id'];
	}
	var sortedList = allProject.sort();
	for (var i = 0; i < sortedList.length; i++) {
		project = sortedList[i].split('~~');
		html += '<option value="' + project[2] + '" title="' + project[0] + ' - ' + project[1] + '">' + project[0] + ' - ' + project[1] + '</option>';
	}
	allProjectList = html;
	$("#project_filter").html(html);
}

// To load the Assigned to list dynamically with all the assigned user list
function loadAllAssignedList() {
	var allUsers = new Array();
	var sortedList = new Array();
	userid = '';
	html = '<option value="-1">Show All</option>';
	for (var i = 0; i < qualityList.length; i++) {
		for (var e = 0; e < qualityList[i]['quality'].length; e++) {
			userid = parseInt(qualityList[i]['quality'][e]['assigned_to_id']);
			allUsers[userid] = qualityList[i]['quality'][e]['assigned_to']+'~~'+userid;
		}
	}
	var sortedList = allUsers.sort();
	for (var i = 0; i < sortedList.length; i++) {
		if(sortedList[i] != null){
			user = sortedList[i].split('~~');
			html += '<option value="' + user[1] + '">' + user[0] + '</option>';
		}
	}
	allAssignedList = html;
	$("#assigned_filter").html(html);
}

// To load the Assigned to list dynamically with the assigned user list based on the selected company and project
function loadAssignedList(assignedToId) {
	clientId = document.getElementById("client_filter").value;
	projectId = document.getElementById("project_filter").value;

	if(clientId == '-1' && projectId == '-1'){
		html = allAssignedList;
	}else{
		var allUsers = new Array();
		var sortedList = new Array();
		userid = '';
		addFlag = false;
		html = '<option value="-1">Show All</option>';
		for (var i = 0; i < qualityList.length; i++) {
			addFlag = false;
			if(qualityList[i]['project_id'] == projectId){
				addFlag = true;
			}else if(clientId != '-1' && qualityList[i]['client'] == clientId && projectId == '-1'){
				addFlag = true;
			}
			if(addFlag){
				for (var e = 0; e < qualityList[i]['quality'].length; e++) {
					userid = parseInt(qualityList[i]['quality'][e]['assigned_to_id']);
					allUsers[userid] = qualityList[i]['quality'][e]['assigned_to']+'~~'+userid;
				}
			}
		}
		var sortedList = allUsers.sort();
		for (var i = 0; i < sortedList.length; i++) {
			if(sortedList[i] != null){
				user = sortedList[i].split('~~');
				html += '<option value="' + user[1] + '">' + user[0] + '</option>';
			}
		}
	}
	$("#assigned_filter").html(html);
	$('#assigned_filter').val(assignedToId);
}

function displayWorkorders() {
	html = "";
	var exp = /((https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
	clientId = document.getElementById("client_filter").value;
	projectId = document.getElementById("project_filter").value;
	statusId = document.getElementById("status_filter").value;
	assignedTo = document.getElementById("assigned_filter").value;
	severityID = document.getElementById("severity_filter").value;
	var statusActiveArray = [];
	var qaStatusStatus = [];
	if(statusId=='99')
	{
		statusActiveArray["Feedback Provided"] = "Feedback Provided";
		statusActiveArray["Fixed"] = "Fixed";
		//statusActiveArray["Hold"] = "Hold";
		statusActiveArray["In Progress"] = "In Progress";
		statusActiveArray["Need More Info"] = "Need More Info";
		statusActiveArray["New"] = "New";
		statusActiveArray["Rejected"] = "Rejected";
		statusActiveArray["Reopened"] = "Reopened";
	}else{
		statusActiveArray[statusId] = statusId;
	}

	var lastComp;
	for (var i = 0; i < qualityList.length; i++) {
			html_top = '';
			html_body = '';
			html_bottom = '';
			qaStatusStatus = [];
			if ((clientId < 0 || clientId == qualityList[i]['client']) && (projectId < 0 || projectId == qualityList[i]['project_id'])) {
				proj_id = qualityList[i]['project_id'];
				//changes for LH#18412------------------------------
				var counter = 0;
				for (var e = 0; e < qualityList[i]['quality'].length; e++) {
				if ((statusActiveArray[statusId] < 0 || statusActiveArray[qualityList[i]['quality'][e]['status']] == qualityList[i]['quality'][e]['status'] ) &&
						(severityID < 0 || severityID == qualityList[i]['quality'][e]['severity_id']) &&
						(assignedTo < 0 || (assignedTo > 0 && assignedTo == qualityList[i]['quality'][e]['assigned_to_id']))) {counter++;}}
				
				var showcounter;
				if (clientId =='-1' && projectId =='-1' && statusId =='-1' && assignedTo =='-1' && severityID =='-1')
				{ showcounter=qualityList[i]['quality'].length;}
				else
				{ showcounter = counter+' / '+qualityList[i]['quality'].length; }	
				//--------------------------------------
				//html_top += '<div class="title_small"><h6>' + qualityList[i]['project_code'] + ' - ' + qualityList[i]['project_name'] + ' :<b> Total - </b><a href="javascript:void(0);" class="qastatslink"onclick="showStats('+proj_id+');"><b>'+counter+'/'+qualityList[i]['quality'].length+'</b> </a></h6></div>';
				 html_top += '<div class="title_small"><h6>' + qualityList[i]['project_code'] + ' - ' + qualityList[i]['project_name'] + ' :<b> Total - </b><a href="javascript:void(0);" class="qastatslink"onclick="showStats('+proj_id+');"><b>'+ showcounter +'</b> </a></h6></div>';
				
				html_top += '<div class="quality_rows">';
				for (var e = 0; e < qualityList[i]['quality'].length; e++) {

					var defectCount = 0;
					if ((statusActiveArray[statusId] < 0 || statusActiveArray[qualityList[i]['quality'][e]['status']] == qualityList[i]['quality'][e]['status'] ) &&
						(severityID < 0 || severityID == qualityList[i]['quality'][e]['severity_id']) &&
						(assignedTo < 0 || (assignedTo > 0 && assignedTo == qualityList[i]['quality'][e]['assigned_to_id']))) {

						tClass = qualityList[i]['quality'][e]['class'];
						html_body += '<dl id="' + qualityList[i]['quality'][e]['id'] + '" class="' + tClass + '">';
						var defect_id = qualityList[i]['quality'][e]['id'];
        				    var lastAction = '';
					qa_status = qualityList[i]['quality'][e]['status'];
					if(qaStatusStatus[qa_status]==null || qaStatusStatus[qa_status] =='')
					{
						qaStatusStatus[qa_status] = 1;
					}
					else
					{
						qaStatusStatus[qa_status]=parseInt(qaStatusStatus[qa_status],10)+1;
					}


						html_body += '<dd class="archivecheck" style="display:none;">';
						html_body += '<input type="checkbox" name="wo_archive_list" value="'+defect_id+'">';
						html_body += '</dd>';

						html_body += '<dd class="id"><a href="/quality/index/edit/?defect_id=' + qualityList[i]['quality'][e]['id'] + '">' + defect_id + '</a></dd>';
						html_body += '<dd class="overdue">';
						if(qualityList[i]['quality'][e]['overdue_flag'] == '1'){
							html_body += '<img src="/_images/flag_icon_red.png" title="Over due"/>';
						}
						html_body += '</dd>';
						html_body += '<dt class="title" title="' + qualityList[i]['quality'][e]['full_title'] + '"><a href="/quality/index/edit/?defect_id=' + qualityList[i]['quality'][e]['id'] + '">' + qualityList[i]['quality'][e]['title'] + '</a></dt>';

						html_body += '<dd class="severity" >';
						html_body += '<span id="severity_' + defect_id + '" onClick="qaShowSeverity(' + defect_id + ',\''+qualityList[i]['quality'][e]['severity']+'\');" >';
						html_body += qualityList[i]['quality'][e]['severity'];
						html_body += '</span><select id="severity_select_' + defect_id + '" style="display: none;" onChange="changeSeverity(this.value, ' + defect_id + ');">';
						html_body += '</select></dd>';

						html_body += '<dd style="height: 27px;" class="status" id="status' + qualityList[i]['quality'][e]['id'] + '">';
						html_body += '<span id="status_' + defect_id + '" >';
						html_body += qualityList[i]['quality'][e]['status'];
						html_body += '</span></dd>';
						
						html_body += '<dd class="category" >';
						html_body += '<span id="category_' + defect_id + '" onClick="qaShowCategory(' + defect_id + ',\''+qualityList[i]['quality'][e]['category']+'\');">';
						html_body += qualityList[i]['quality'][e]['category'];
						html_body += '</span><select id="category_select_' + defect_id + '" style="display: none;" onChange="changeCategory(this.value, ' + defect_id + ');">';
						html_body += '</select></dd>';

						html_body += '<dd class="version">' + qualityList[i]['quality'][e]['version'] + '</dd>';
						html_body += '<dd class="opendate">' + qualityList[i]['quality'][e]['open_date'] + '</dd>';
						
						html_body += '<dd style="height: 27px;" class="assigned" id="assigned_' + qualityList[i]['quality'][e]['id'] + '">';
						html_body += '<span id="assigned_a_' + defect_id + '" >';
						html_body += qualityList[i]['quality'][e]['assigned_to'];
						html_body += '</span></dd>';

						html_body += '<dd class="detected_by">' + qualityList[i]['quality'][e]['detected_by'] + '</dd>';
					
						lastAction = qualityList[i]['quality'][e]['qa_last_action'];
					
						if(qualityList[i]['quality'][e]['wo_last_comment_date']!='N/A' || qualityList[i]['quality'][e]['wo_last_comment_user'] != 'N/A')
						{					
							html_body += '<dd class="lastaction" style="text-align:center;" onmouseover="showComment('+defect_id  +');" onmouseout="hideComment('+defect_id  +');">';
							html_body += lastAction; 
							html_body += '</dd>';							
						}
						else
						{
							html_body += '<dd class="lastaction" style="text-align:center;">';
							html_body += lastAction;
							html_body += '</dd>';
						}
						if(qualityList[i]['quality'][e]['wo_last_comment']!=null)
						var replacedText = (qualityList[i]['quality'][e]['wo_last_comment']).replace(exp,"<a href='$1' target='_blank'>$1</a>");
						html_body += '<dd id ="wo_comment_'+defect_id+'" class="wo_comment" style="display:none;"  onmouseover="showComment('+defect_id  +');" onmouseout="hideComment('+defect_id +');"><div class="wo_comment_header"></div><div class="wo_comment_content"><p class="risk_desc">';
						html_body += replacedText;
						html_body += '</p></div><div class="wo_comment_footer"></div></dd>';
						html_body += '</dl>';
					}			
				}
				html_bottom += '</div>';
				html_body += '<div id ="qa_stats_'+proj_id+'" class="message_qa_stats"><h3><u>Status Overview</u></h3><p>';
						html_body += '<br>New - '+qastatsDefaultValue(qaStatusStatus["New"]);
						html_body += '<br>Reopened - '+qastatsDefaultValue(qaStatusStatus["Reopened"]);
						html_body += '<br>In Progress - '+qastatsDefaultValue(qaStatusStatus["In Progress"]);
						html_body += '<br>Need More Info - '+qastatsDefaultValue(qaStatusStatus["Need More Info"]);
						html_body += '<br>Feedback Provided - '+qastatsDefaultValue(qaStatusStatus["Feedback Provided"]);
						html_body += '<br>Rejected - '+qastatsDefaultValue(qaStatusStatus["Rejected"]);
						html_body += '<br>Hold - '+qastatsDefaultValue(qaStatusStatus["Hold"]);
						html_body += '<br>Fixed - '+qastatsDefaultValue(qaStatusStatus["Fixed"]);
						html_body += '<br>Closed - '+qastatsDefaultValue(qaStatusStatus["Closed"]);
						html_body += '<br>------------------------------------------';
						html_body += '<br><b>Total - '+qualityList[i]['quality'].length+'</b>';						
						html_body += '</p>';
						html_body += '<div style="clear: both;"></div>';
						html_body += '<div class="qa_stats_buttons">';
						html_body += '<button onClick="$(\'#qa_stats_'+proj_id+'\').css({display:\'none\'}); return false;"><span>Close</span></button>';
						html_body += '<div style="clear: both;"></div>';
						html_body += '</div></div>';
				
				if(html_body != ''){
					if(qualityList[i]['client'] != lastComp) {
						html_company_top = '<div class="title_small"><h5>' + qualityList[i]['company_name'] + '</h5></div>';
					}else{
						html_company_top = '';
					}
					html += html_company_top + html_top + html_body + html_bottom;
					lastComp = qualityList[i]['client'];
				}
				html += '<input type=hidden id="active_wo" value=5>'; 
			}
	}		
  severity_value = $('#severity_filter').val();
	Set_Cookie( "lighthouse_qa_data", clientId + '~' + projectId + '~' + statusId + '~' + assignedTo + '~' + severity_value, "7", "/", "", "");
	Set_Cookie( "lh_qa_project_cookie", "", "7", "/", "", "");	

	$("#wo_containter").html(html);
	loadAssignedList(assignedTo);
}

function qastatsDefaultValue(qaStatusStatus)
{
	if(qaStatusStatus==null || qaStatusStatus =='')
	{
		qaStatusStatus = 0;
	}
	return qaStatusStatus;
}
function archiveWO_CheckList(){
	var qualityList = '';
	for (i=0; i<document.getElementsByName('wo_archive_list').length; i++){
		if (document.getElementsByName('wo_archive_list')[i].checked==true)
		{			
			qualityList = qualityList+','+document.getElementsByName('wo_archive_list')[i].value;		
		}
	}
	
	if(qualityList!='')
	{
		$('.message_archive').css({display:'block'});
		document.getElementById('active_wo').value = qualityList;	
	}else
	{
		$('.message_archive_select_check').css({display:'block'});
	}
}

function unarchiveWO_CheckList(){

	var qualityList = '';
	for (i=0; i<document.getElementsByName('wo_archive_list').length; i++){
		if (document.getElementsByName('wo_archive_list')[i].checked==true)
		{			
			qualityList = qualityList+','+document.getElementsByName('wo_archive_list')[i].value;		
		}
	}
	
	if(qualityList!='')
	{
		$('.message_unarchive').css({display:'block'});
		document.getElementById('active_wo').value = qualityList;	
	}else
	{
		$('.message_archive_select_check').css({display:'block'});
	}
}

function showComment(defect_id,flag){
	var topVar = ($('#wo_comment_'+defect_id).offset().top + 58);	
	var leftVar = 710;	
	$('#wo_comment_'+defect_id).css({display:'block', left: leftVar + 'px'});
}

function showStats(proj_id)
{
	//var topVar = ($('#wo_comment_'+defect_id).offset().top + 58);	
	//var leftVar = 710;	
//	$('#qa_stats_'+proj_id).css({display:'block', left: leftVar + 'px'});
	$('#qa_stats_'+proj_id).css({display:'block'});
}

function hideComment(defect_id){
	$('#wo_comment_'+defect_id).css({display:'none'});
}

function changeQASeverity()
{
}
var displayedOrder="asc";
    

function sortQuality(sortType){
  $('#wo_dimmer_ajax').css({display:'block'});
//  jQuery.getJSON('/_ajaxphp/quality_json.php', function(json) {
//  	qualityList = json;
//  	if(isOnPageload && sortType!="title" ){
//        displayedOrder="asc";
//        sortDir = 0;	
//  	}
//  	if(sortType=="title"&&isOnPageload){
//  		 displayedOrder="desc";
// 		 sortDir = 1;
//  	}
  	if (currentSort == sortType) {
  		if (sortDir == 1) {
  			sortDir = 0;
 // 			displayedOrder="asc";
  		} else {
 // 			displayedOrder="desc";
  			sortDir = 1;
  		} 
  	}else{
      if(typeof sortDir == "undefined"){	
      //  displayedOrder="desc";
        sortDir = 1;	
      }
    }
  	if(sortDir == 0){
  	 Set_Cookie("selectedSortOption",sortType+":0","","/");
  	 Set_Cookie("selectedSortOption",sortType+":0","","/quality");
  	 Set_Cookie("selectedSortOption",sortType+":0","","/quality/");
      }else{
  	 Set_Cookie("selectedSortOption",sortType+":1","","/");
  	 Set_Cookie("selectedSortOption",sortType+":1","","/quality");	
  	 Set_Cookie("selectedSortOption",sortType+":1","","/quality/");
  	}
 //     isOnPageload = false;
  	for (var i = 0; i < qualityList.length; i++) {
  		tList = qualityList[i]['quality'];
  		if(sortDir == 1){
        tList.reverse();
  		}
      switch (sortType) {
  			case "id":
  				tList.sort(sortById);
  				break;
  			case "title":
  				tList.sort(sortByTitle);
  				break;
  			case "severity":
  				tList.sort(sortBySeverity);
  				break;
  			case "status":
  				tList.sort(sortByStatus);
  				break;
  			case "category":
  				tList.sort(sortByCategory);
  				break;
  			case "version":
  				tList.sort(sortByVersion);
  				break;		
  			case "assigned_to":
  				tList.sort(sortByAssignedTo);
  				break;
  			case "open_date":
  				tList.sort(sortByOpenDate);
  				break;
  			case "detected_by":
  				tList.sort(sortByDetectedBy);
  				break;
  			case "last_action":
  				tList.sort(sortByLast_action);
  				break;
  			default:
  		}
  		if (sortDir == 1) {	
      	tList.reverse();
      }
      qualityList[i]['quality'] = tList;
  	}
  	
  	$(".quality_sort li a").removeClass("up").removeClass("down");
  	
  	if (sortDir == 1) {
  		$("#" + sortType + "sort").addClass("down").removeClass("up");
  	} else {
  		$("#" + sortType + "sort").removeClass("down").addClass("up");
  	}
  	
  /*	$(".quality_sort li a").removeClass("down").removeClass("up");
  	
  	if (sortDir == 1) {
  		$("#" + sortType + "sort").addClass("up").removeClass("down");
  	} else {
  		$("#" + sortType + "sort").removeClass("up").addClass("down");
  	}  */	
  
  	currentSort = sortType;
  	displayWorkorders();
		$('#wo_dimmer_ajax').css({display:'none'});
  //});
}


//*************************
//**Custom sort functions**
//*************************	
function sortByTitle(a, b) {
  if(a["title"]==null || b["title"]==null){return 0;}else {  var x = a["title"].replace(/^\s+|\s+$/g, "").toLowerCase();//.toLowerCase();
    var y = b["title"].replace(/^\s+|\s+$/g, "").toLowerCase();//.toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));}
}

function sortByLast_action(a, b) {
    var x = a["last_log_date"];
    var y = b["last_log_date"];
    return ((x > y) ? -1 : ((x < y) ? 1 : 0));
}

function sortByReqType(a, b) {
    var x = a["req_type"].toLowerCase();
    var y = b["req_type"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByStatus(a, b) {
    var x = a["status"].toLowerCase();
    var y = b["status"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortBySeverity(a, b) {
    var x = a["severity"].toLowerCase();
    var y = b["severity"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByCategory(a, b) {
    var x = a["category"].toLowerCase();
    var y = b["category"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByVersion(a, b) {
    var x = a["version"].toLowerCase();
    var y = b["version"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByAssignedTo(a, b) {
    var x = a["assigned_to"].toLowerCase();
    var y = b["assigned_to"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByDetectedBy(a, b) {
    var x = a["detected_by"].toLowerCase();
    var y = b["detected_by"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByOpenDate(a, b) {
    var x = a["creation_date"];
    var y = b["creation_date"];
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByAssignedDate(a, b) {
    var x = a["assigned_date"].toLowerCase();
    var y = b["assigned_date"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortById(a, b) {
    var x = parseInt(a["id"]);
    var y = parseInt(b["id"]);
	return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function generateWOReport(){
	var typeFilter = '1';
	var rp_client_filter =  $("#client_filter").val();
	var rp_project_filter =  $("#project_filter").val();
	var rp_status_filter =  $("#status_filter").val();
	var rp_severity_filter =  $("#severity_filter :selected").text();
	var rp_assigned_filter =  $("#assigned_filter").val();

	window.open('/_ajaxphp/quality_json.php?report=excel&rp_client_filter='+rp_client_filter+'&rp_project_filter='+rp_project_filter+'&rp_status_filter='+rp_status_filter+'&rp_severity_filter='+rp_severity_filter+'&rp_assigned_filter='+rp_assigned_filter);
}

function gotoWorkorder(){
	var defectId = document.getElementById("defect_id").value;
	if(defectId == "" || defectId == "id #") {
		alert("Please enter a Defect ID");
	} else {
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/qa_exist_check.php?defectId="+defectId,
			success: function(msg) {
				if(msg == "1"){
					window.location = "/quality/index/edit/?defect_id="+defectId;
				}else{
					alert("Defect ID does not exist.");
				}
			}
		});
	}
	return false;
}

function CreateDefect()
{
	projectId = document.getElementById("project_filter").value;
	Set_Cookie( "lh_qa_project_cookie", projectId , "7", "/", "", "");
	window.location = '/quality/index/create/';
}




