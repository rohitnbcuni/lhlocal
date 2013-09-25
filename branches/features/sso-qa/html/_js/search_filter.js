//Quick Filter Sort
var workorderList = Array();
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
var allRequestedbyList;
var projectList;
var assignedToList;
var requestedbyList;
var page_no = '1';
var prevSelectedProject ;
var prevSelectedAssignedTo ;
var prevSelectedrequestedby;
var pages = 5;    // number of pages to be displayed
var column_name = 'title';
var sort_order = '1';
var page_num = '1';

var ajaxRequest;  // The variable that makes Ajax possible!

$(document).ready(function() {
	privacyLookup[0] = "Low";
	privacyLookup[1] = "Medium";
	privacyLookup[2] = "High";
	
	statusLookup[0] = "Show All";
	statusLookup[1] = "Assigned";
	statusLookup[2] = "Closed";
	$('#wo_dimmer_ajax').css({display:'block'});
	
	// Options displayed in comma-separated list
	 var requestTypeFilter = '';
	$("#control_7").multiSelect({ selectAll: false, oneOrMoreSelected: '*' }, function (json){
		requestTypeFilter = '';
		   $(".multiSelectOptions input[type=checkbox]:checked").each(function(){
				if( $(this).val() != '' ) {				
					requestTypeFilter = requestTypeFilter+','+$(this).val();
				}
		   });	 
		  $("#requestTypeFilter").val(requestTypeFilter);
			displayWorkorders('1','first','title','1','3');
	});
	
	 $(".multiSelectOptions input[type=checkbox]:checked").each(function(){
			if( $(this).val() != '' ) {				
				requestTypeFilter = requestTypeFilter+','+$(this).val();
			}
	   });	 
	  $("#requestTypeFilter").val(requestTypeFilter);
  if(document.getElementById("project_status_filter")){
	var status = document.getElementById("project_status_filter").value; 
  }

  if(status == 1)
	{	
		$('#archiveBTN').html('<span>archive</span>');
		$('#archiveBTN').unbind('click');
		$('#archiveBTN').attr('onclick','');
		$('#archiveBTN').bind('click', function() {
			 archiveWO_CheckList();
		});
	}
	else if(status == 0)
	{
		$('#archiveBTN').html('<span>Unarchive</span>');
		$('#archiveBTN').unbind('click');
		$('#archiveBTN').attr('onclick','');		
		$('#archiveBTN').bind('click', function() {
			 unarchiveWO_CheckList();
		});
	}
  else{
    $('#archiveBTN').html('<span>Activate</span>');
		$('#archiveBTN').unbind('click');
		$('#archiveBTN').attr('onclick','');		
		$('#archiveBTN').bind('click', function() {
			 activeWO_CheckList();
		});
  }
  //cookie_date = getCookie("lighthouse_wo_search_data");
  var woLists = $('#woLists').val();	
	//jQuery.getJSON('/_ajaxphp/search.php?status='+status, function(json) {
  $.ajax({
      url: "/_ajaxphp/search.php",
      type: "POST",
      data: {status:status, idess:woLists},
      dataType: "JSON",
      success: function(json){
		workorderList = json;
		cookie_date = '';//getCookie("lighthouse_wo_data");
		$("#wo_containter .title_small").css({display:"none"});
		$("#wo_containter .workorders_rows").css({display:"none"});
//		wo_sortWorkorders("title");
		//wo_loadAllProjectList();
		wo_loadAllAssignedList();
		wo_loadAllRequestedbyList();
		
		sortDir = 1;
		sortWorkorders("id");
	
		displayWorkorders();
      
		$("#wo_containter .title_small").css({display:"block"});
		$("#wo_containter .workorders_rows").css({display:"block"});
		$('#wo_dimmer_ajax').css({display:'none'});
      }
	});
});


function getWO_On_Status(){
	

	 displayWorkorders();
    }

function woShowStatus(theId,status_id) {
	if($('#client_login').val() != "client") {
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/wo_status_list.php?status_id="+status_id,
			success: function(msg) {
				if($('#status_select_'+theId).html() == "") {
					$('#status_select_'+theId).html(msg);
					$('#status_select_'+theId).bind('blur', function(){
						woHideStatus(theId);
					});
				}
				$('#status_'+theId).css({display:'none'});
				$('#status_select_'+theId).css({display:'block'});
				$('#status_select_'+theId).focus();
			}
		});
	}
}
function woHideStatus(theId) {
	$('#status_'+theId).css({display:'block'});
	$('#status_select_'+theId).css({display:'none'});
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
function woShowAssigned(theId) {
	if($('#client_login').val() != "client") {
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
}

function woHideAssigned(theId) {
	$('#assigned_a_'+theId).css({display:'block'});
	$('#assigned_select_'+theId).css({display:'none'});
}

function changeAssigned(selectVal, theId) {
	
	var sel = document.getElementById('assigned_select_'+theId);
	var opts = sel.getElementsByTagName('option');
	var name = "";
	for(var i = 0; i < opts.length; i++) {
		if(opts[i].selected) {
			out = opts[i].text;
		}
	}
	var outPart = out.split(' ');
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/wo_change_assigned.php?id="+theId+"&user_id="+selectVal,
		success: function(msg) {
			var oldHTML = $('assigned_a_'+theId).html();
			//$('#assigned_a_'+theId).html(outPart[0]+" "+outPart[1]);
			
			if(out.length > 20){
				var shortText = jQuery.trim(out).substring(0, 20).split(" ").slice(0, 4).join(" ") + "...";
			}else{
				shortText = out;
			}
			$('#assigned_a_'+theId).attr("title",out);
			$('#assigned_a_'+theId).html(shortText);
			$('#'+theId+' .status').html('New');
			woHideAssigned(theId);
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
	selectedRow = -1;
	var comp = -1;
	var tRemove = -1;


		
	//$('#'+theId).css({display:'none'});
	
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

function activeWo(theId) {
	$('.message_active').css({display:'none'});
	document.getElementById('active_wo').value = "";
	selectedRow = -1;
	var comp = -1;
	var tRemove = -1;


		
	//$('#'+theId).css({display:'none'});
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/wo_active.php?id="+theId,
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
	selectedRow = -1;
	var comp = -1;
	var tRemove = -1;
		
	//$('#'+theId).css({display:'none'});
	
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
function wo_changeCompany(){
	//wo_loadProjectList(); 
	wo_loadAssignedList	();
	wo_loadRequestedbyList
      displayWorkorders();
}

// To Load the project list dynamically with the projects of the selected company
function wo_loadProjectList() {
	var clientId = '';
	if(document.getElementById("client_filter")){
	clientId = document.getElementById("client_filter").value;
	}
	html = '<option value="-1">Show All</option>';
  if($('#project_status_filter').val() == '0') {
    if(projectList != null){
  		html = '<option value="-1">Show All</option>';
      for(var i = 0; i<projectList[0].length; i++){
  			if(projectList[0][i] == prevSelectedProject){
         html += '<option selected title="'+ projectList[1][i] + ' - ' + projectList[2][i] + '" value="' + projectList[0][i] + '">'+ projectList[1][i] + ' - ' + projectList[2][i] + '</option>';        
        } else { 
         html += '<option value="' + projectList[0][i] + '" title="'+ projectList[1][i] + ' - ' + projectList[2][i] + '">'+ projectList[1][i] + ' - ' + projectList[2][i] + '</option>';
        }    
      }	
    }
  } else if(clientId == '-1'){
		html = allProjectList;
	} else{
		html = '<option value="-1">Show All</option>';
		for (var i = 0; i < workorderList.length; i++) {
			if(clientId == "-1" || clientId == workorderList[i]['client']){
				html += '<option value="' + workorderList[i]['project_id'] + '" title="'+ workorderList[i]['project_code'] + ' - ' + workorderList[i]['project_name'] + '">'+ workorderList[i]['project_code'] + ' - ' + workorderList[i]['project_name'] + '</option>';
			}
		}
	}
	$("#project_filter").html(html);
}

// To Load the project list dynamically with all the project for the first time
function wo_loadAllProjectList() {
	if(document.getElementById("project_filter")){
		previouslySelectedProject=document.getElementById("project_filter").value;
	}
	if(document.getElementById("client_filter")){
		clientId = document.getElementById("client_filter").value;
	}
	html = '<option value="-1">Show All</option>';
  var allProject = new Array();
	var sortedList = new Array();
  if($('#project_status_filter').val() == '0') {
    if(projectList != null){
      for(var i = 0; i<projectList[0].length; i++){
  			if(projectList[0][i] == prevSelectedProject){
         html += '<option title="'+ projectList[1][i] + ' - ' + projectList[2][i] + '" selected value="' + projectList[0][i] + '">'+ projectList[1][i] + ' - ' + projectList[2][i] + '</option>';        
        } else { 
         html += '<option title="'+ projectList[1][i] + ' - ' + projectList[2][i] + '" value="' + projectList[0][i] + '">'+ projectList[1][i] + ' - ' + projectList[2][i] + '</option>';
        }    
      }	
    }
  } else {
  	for (var i = 0; i < workorderList.length; i++) {
  		allProject[i] = workorderList[i]['project_code']+'~~'+workorderList[i]['project_name']+'~~'+workorderList[i]['project_id'];
  	}
  	var allProject2 = [];
  	if(allProject.length > 0){
  		allProject2 = DistinctArray(allProject, true);
	  	if(allProject2.length > 0){
	  		allProject = allProject2;
	  	}
  	}
  	var sortedList = allProject.sort();
  	for (var i = 0; i < sortedList.length; i++) {
  		project = sortedList[i].split('~~');
		if(previouslySelectedProject==project[2]){
	  		html += '<option title="' + project[0] + ' - ' + project[1] + '" SELECTED value="' + project[2] + '">' + project[0] + ' - ' + project[1] + '</option>';
		}else{
	  		html += '<option title="' + project[0] + ' - ' + project[1] + '" value="' + project[2] + '">' + project[0] + ' - ' + project[1] + '</option>';
		}
  	}
  	allProjectList = html;
	}
	$("#project_filter").html(html);
  
}

// To load the Assigned to list dynamically with all the assigned user list
function wo_loadAllAssignedList() {
	var allUsers = new Array();
	var sortedList = new Array();
	userid = '';
	html = '<option value="-1">Show All</option>';
  if($('#project_status_filter').val() == '0'){
    if(assignedToList != null){
//  		html = '<option value="-1">Show All</option>';
      for(var i = 0; i<assignedToList[0].length; i++){
   		  if(prevSelectedAssignedTo == assignedToList[0][i]){ 	
          html += '<option  selected value="' + assignedToList[0][i] + '">' + assignedToList[1][i] + '</option>';    
        } else {
          html += '<option value="' + assignedToList[0][i] + '">' + assignedToList[1][i] + '</option>';        
        }
      }	
    }
  } else {
  	for (var i = 0; i < workorderList.length; i++) {
  		for (var e = 0; e < workorderList[i]['workorders'].length; e++) {
  			userid = parseInt(workorderList[i]['workorders'][e]['assigned_to_id']);
  			allUsers[userid] = workorderList[i]['workorders'][e]['assigned_to']+'~~'+userid;
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
  }
	$("#assigned_filter").html(html);
}

// To load the Assigned to list dynamically with the assigned user list based on the selected company and project
function wo_loadAssignedList(assignedToId) {
	var clientId = '';
	var projectId = '';
	if(document.getElementById("client_filter")){
	clientId = document.getElementById("client_filter").value;
	}
	if(document.getElementById("project_filter")){
	projectId = document.getElementById("project_filter").value;
	}

  html = '<option value="-1">Show All</option>';
	if(clientId == '-1' && projectId == '-1'){
		html = allAssignedList;
	} else if($('#project_status_filter').val() == '0'){
     if(assignedToList != null){
//  		html = '<option value="-1">Show All</option>';
      for(var i = 0; i<assignedToList[0].length; i++){
   		  if(prevSelectedAssignedTo == assignedToList[0][i]){ 	
          html += '<option  selected value="' + assignedToList[0][i] + '">' + assignedToList[1][i] + '</option>';    
        } else {
          html += '<option value="' + assignedToList[0][i] + '">' + assignedToList[1][i] + '</option>';        
        }    
      }	
    }
  } else{
		var allUsers = new Array();
		var sortedList = new Array();
		userid = '';
		addFlag = false;
		html = '<option value="-1">Show All</option>';
		for (var i = 0; i < workorderList.length; i++) {
			addFlag = false;
			if(workorderList[i]['project_id'] == projectId){
				addFlag = true;
			}else if(clientId != '-1' && workorderList[i]['client'] == clientId && projectId == '-1'){
				addFlag = true;
			}
			if(addFlag){
				for (var e = 0; e < workorderList[i]['workorders'].length; e++) {
					userid = parseInt(workorderList[i]['workorders'][e]['assigned_to_id']);
					allUsers[userid] = workorderList[i]['workorders'][e]['assigned_to']+'~~'+userid;
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


// To load the requested by list dynamically with all the assigned user list
function wo_loadAllRequestedbyList() {
	var allUsers = new Array();
	var sortedList = new Array();
	userid = '';
	html = '<option value="-1">Show All</option>';
  if($('#project_status_filter').val() == '0'){
    if(requestedbyList != null){
//  		html = '<option value="-1">Show All</option>';
      for(var i = 0; i<requestedbyList[0].length; i++){
   		  if(prevSelectedrequestedby == requestedbyList[0][i]){ 	
          html += '<option  selected value="' + requestedbyList[0][i] + '">' + requestedbyList[1][i] + '</option>';    
        } else {
          html += '<option value="' + requestedbyList[0][i] + '">' + requestedbyList[1][i] + '</option>';        
        }
      }	
    }
  } else {
  	for (var i = 0; i < workorderList.length; i++) {
  		for (var e = 0; e < workorderList[i]['workorders'].length; e++) {
  			userid = parseInt(workorderList[i]['workorders'][e]['requested_by_id']);
  			allUsers[userid] = workorderList[i]['workorders'][e]['requested_by']+'~~'+userid;
  		}
  	}
  	var sortedList = allUsers.sort();
  	for (var i = 0; i < sortedList.length; i++) {
  		if(sortedList[i] != null){
  			user = sortedList[i].split('~~');
  			html += '<option value="' + user[1] + '">' + user[0] + '</option>';
  		}
  	}
  	allRequestedbyList = html;
  }
	$("#requestedby_filter").html(html);
}

// To load the requested by user list dynamically with the assigned user list based on the selected company and project
function wo_loadRequestedbyList(requestedbyId) {

	clientId = document.getElementById("client_filter").value;
	projectId = document.getElementById("project_filter").value;
	assignedTo=document.getElementById("assigned_filter").value;
  html = '<option value="-1">Show All</option>';
	if(clientId == '-1' && projectId == '-1'  && assignedTo == '-1'){
		html = allRequestedbyList;
	} else if($('#project_status_filter').val() == '0'){
     if(requestedbyList != null){
//  		html = '<option value="-1">Show All</option>';
      for(var i = 0; i<requestedbyList[0].length; i++){
   		  if(prevSelectedrequestedby == requestedbyList[0][i]){ 	
          html += '<option  selected value="' + requestedbyList[0][i] + '">' + requestedbyList[1][i] + '</option>';    
        } else {
          html += '<option value="' + requestedbyList[0][i] + '">' + requestedbyList[1][i] + '</option>';        
        }    
      }	
    }
  } else{
		var allUsers = new Array();
		var sortedList = new Array();
		userid = '';
		addFlag = false;
		html = '<option value="-1">Show All</option>';
		for (var i = 0; i < workorderList.length; i++) {
			addFlag = false;
			if(workorderList[i]['project_id'] == projectId){
				addFlag = true;
			}else if(clientId != '-1' && workorderList[i]['client'] == clientId && projectId == '-1'){
				addFlag = true;
			}
			if(addFlag){ 
				for (var e = 0; e < workorderList[i]['workorders'].length; e++) {
					userid = parseInt(workorderList[i]['workorders'][e]['requested_by_id']);
					allUsers[userid] = workorderList[i]['workorders'][e]['requested_by']+'~~'+userid;
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
	$("#requestedby_filter").html(html);
	$('#requestedby_filter').val(requestedbyId);
}





function displayWorkorders(page_no,isNextClicked,column,order,isFilterClicked) {

			  buildWorkordersHTML();              // for active and draft workorders

}
function DistinctArray(array,ignorecase) {
    if(typeof ignorecase =="undefined"||array==null||array.length==0) return null;
    if(typeof ignorecase =="undefined") ignorecase=false;
    var sMatchedItems="";
    var foundCounter=0;
    var newArray=[];
    for(var i=0;i<array.length;i++) {
        var sFind=ignorecase?array[i].toLowerCase():array[i];
        if(sMatchedItems.indexOf("|"+sFind+"|")<0) {
            sMatchedItems+="|"+sFind+"|";
            newArray[foundCounter++]=array[i];
        }
    }
    return newArray;
}

function buildWorkordersHTML() {
	html = "";
    var exp = /((https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;


	//alert("sss");
	clientId = document.getElementById("client_filter").value;
	//projectId = document.getElementById("project_filter").value;
	statusId = document.getElementById("status_filter").value;
	assignedTo = document.getElementById("assigned_filter").value;
	requestedby = document.getElementById("requestedby_filter").value;
//alert(requestedby);
	// Added this fix for finding previously selected project exists -Chandra
	//var isProjectSelectedPresent = 0;
	//alert(workorderList.toSource());
	for (var i = 0; i < workorderList.length; i++) {
		if(workorderList[i]['company_id'] == clientId){
			isProjectSelectedPresent = 1;
			break;
		}
	}
	/*if(isProjectSelectedPresent == 0 ) {
		projectId = "-1";
	}*/
	var requestTypeFilter = "";
	if(document.getElementById("requestTypeFilter")!=null)
	{
		requestTypeFilter = document.getElementById("requestTypeFilter").value;
		//alert(requestTypeFilter+"requestTypeFilter");
	}
	var assocReqTypeArray = [];
	assocReqTypeArray["Outage"] = "Outage";
	assocReqTypeArray["Problem"] = "Problem";
	assocReqTypeArray["Change"] = "Change";
	var req_txt = "";
	if(requestTypeFilter != null && requestTypeFilter!='' ){
		reqTypeFilter = requestTypeFilter.split(',');
		if(reqTypeFilter.length > 1)
		{
			 assocReqTypeArray = [];
			for (var i = 0; i < reqTypeFilter.length; i++) {
				if(reqTypeFilter[i]!=null && reqTypeFilter[i]!='')
				{
					assocReqTypeArray[reqTypeFilter[i]] = reqTypeFilter[i];
				}
			}
		}
	}
	var statusActiveArray = [];
	if(statusId=='99')
	{
		statusActiveArray["Feedback Provided"] = "Feedback Provided";
		statusActiveArray["On Hold"] = "On Hold";
		statusActiveArray["In Progress"] = "In Progress";
		statusActiveArray["Need More Info"] = "Need More Info";
		statusActiveArray["New"] = "New";
		statusActiveArray["Rejected"] = "Rejected";
		statusActiveArray["Reopened"] = "Reopened";
	}else{
		statusActiveArray[statusId] = statusId;
	}
	
	var lastComp;
	
	for (var i = 0; i < workorderList.length; i++) {
			
			html_top = '';
			html_body = '';
			html_bottom = '';
			if ((clientId < 0 || clientId == workorderList[i]['client']) ){
				if((i-1 >= 0 && workorderList[i-1]['client'] != workorderList[i]['client']) || i == 0){
					//html_top += '<div class="title_small"><h6>' + workorderList[i]['company_name'] + ' - ' + workorderList[i]['client'] + '</h6></div>';
			}
				//alert(html_top+"test"+workorderList.length);
				html_top += '<div class="workorders_rows">';
				for (var e = 0; e < workorderList[i]['workorders'].length; e++) {
					if (((statusActiveArray[statusId] < 0 || statusActiveArray[workorderList[i]['workorders'][e]['status']] == workorderList[i]['workorders'][e]['status'])|| 
						('over_due' == statusId && '1' == workorderList[i]['workorders'][e]['overdue_flag'])) &&
						(assignedTo < 0 || (assignedTo > 0 && assignedTo == workorderList[i]['workorders'][e]['assigned_to_id'])) && (requestedby < 0 || (requestedby > 0 && requestedby == workorderList[i]['workorders'][e]['requested_by_id']))
&& ((assocReqTypeArray[workorderList[i]['workorders'][e]['req_type']] == workorderList[i]['workorders'][e]['req_type'])||document.getElementById("project_status_filter").value==0 )) {
						tClass = workorderList[i]['workorders'][e]['class'];
						html_body += '<dl id="' + workorderList[i]['workorders'][e]['id'] + '" class="' + tClass + '">';

						var wo_id = workorderList[i]['workorders'][e]['id']

						html_body += '<dd class="archivecheck" >';
						html_body += '<input type="checkbox" name="wo_archive_list" value="'+wo_id+'">';
						html_body += '</dd>';

						html_body += '<dd class="id"><a href="/workorders/index/edit/?wo_id=' + workorderList[i]['workorders'][e]['id'] + '">' + wo_id + '</a></dd>';
						html_body += '<dd class="overdue">';
						if(workorderList[i]['workorders'][e]['overdue_flag'] == '1'){
							html_body += '<img src="/_images/flag_icon_red_new.png" title="Over due"/>';
						}
						html_body += '</dd>';
						html_body += '<dt class="title" title="' + workorderList[i]['workorders'][e]['full_title'] + '"><a href="/workorders/index/edit/?wo_id=' + workorderList[i]['workorders'][e]['id'] + '">' + workorderList[i]['workorders'][e]['title'] + '</a></dt>';
						html_body += '<dd class="req_type">' + workorderList[i]['workorders'][e]['req_type'] + '</dd>';
						//html_body += '<dd class="status">' + workorderList[i]['workorders'][e]['status'] + '</dd>';
						html_body += '<dd style="height: 27px;" class="status" id="status' + workorderList[i]['workorders'][e]['id'] + '">';
						html_body += '<span id="status_' + workorderList[i]['workorders'][e]['id'] + '" onClick="woShowStatus(' + workorderList[i]['workorders'][e]['id'] + ',' + workorderList[i]['workorders'][e]['status_id'] + ');">';
						html_body += workorderList[i]['workorders'][e]['status'];
						html_body += '</span><select id="status_select_' + workorderList[i]['workorders'][e]['id'] + '" style="display: none;" onChange="changeStatus(this.value, ' + workorderList[i]['workorders'][e]['id'] + ');">';
						html_body += '</select></dd>';
						html_body += '<dt class="requested">' + workorderList[i]['workorders'][e]['requested_by'] + '</dt>';
						//html_body += '<dd class="assigned"><!-- <a href="">Vorbeck, Garrett</a> --><select><option></option></select></dd>';
						
						
						html_body += '<dd style="height: 27px;" class="assigned" id="assigned_' + workorderList[i]['workorders'][e]['id'] + '">';
						if(workorderList[i]['workorders'][e]['assigned_to'] != null){
							html_body += '<span title="'+workorderList[i]['workorders'][e]['assigned_to']+'" id="assigned_a_' + workorderList[i]['workorders'][e]['id'] + '" onClick="woShowAssigned(' + workorderList[i]['workorders'][e]['id'] + ');">';
							if(workorderList[i]['workorders'][e]['assigned_to'].length > 22){
								var shortText = jQuery.trim(workorderList[i]['workorders'][e]['assigned_to']).substring(0, 22).split(" ").slice(0, 4).join(" ") + "...";
							}else{
								shortText = workorderList[i]['workorders'][e]['assigned_to'];
							}
						}
						html_body += shortText;
						html_body += '</span><select id="assigned_select_' + workorderList[i]['workorders'][e]['id'] + '" style="display: none;" onChange="changeAssigned(this.value, ' + workorderList[i]['workorders'][e]['id'] + ');">';
						html_body += '</select></dd>';
						
						html_body += '<dd class="opendate">' + workorderList[i]['workorders'][e]['open_date'] + '</dd>';

						html_body += '<dd class="due_date">' + workorderList[i]['workorders'][e]['launch_date'] + '</dd>';
	
						if(workorderList[i]['workorders'][e]['wo_last_comment_date']!='N/A' || workorderList[i]['workorders'][e]['wo_last_comment_user'] != 'N/A')
						{					
							html_body += '<dd class="lastcommentby" onmouseover="showComment('+wo_id  +',1);" onmouseout="hideComment('+wo_id  +');">';
							html_body += workorderList[i]['workorders'][e]['wo_last_comment_user'];
							html_body += '</dd>';			
							html_body += '<dd class="commentdate" >';
							html_body += workorderList[i]['workorders'][e]['wo_last_comment_date'];
							html_body += '</dd>';
						}
						else
						{
							html_body += '<dd class="lastcommentby" style="text-align:center;">';
							html_body += workorderList[i]['workorders'][e]['wo_last_comment_user'];
							html_body += '</dd>';			
							html_body += '<dd class="commentdate" style="text-align:center;">';
							html_body += workorderList[i]['workorders'][e]['wo_last_comment_date'];
							html_body += '</dd>';
						}
						var replacedText = (workorderList[i]['workorders'][e]['wo_last_comment']).replace(exp,"<a href='$1' target='_blank'>$1</a>");
						/**
						 * Ticket no 16857,19352
						 */
						//var replacedText = ( unescape(workorderList[i]['workorders'][e]['wo_last_comment']).replace(/\+/g, " ")).replace(exp,"<a href='$1' target='_blank'>$1</a>");
                        //End Ticket
                        html_body += '<dd id ="wo_comment_'+wo_id+'" class="wo_comment" style="display:none;"  onmouseover="showComment('+wo_id  +',3);" onmouseout="hideComment('+wo_id +');"><div class="wo_comment_header"></div><div class="wo_comment_content"><p class="risk_desc">';
						html_body += replacedText;
						html_body += '</p></div><div class="wo_comment_footer"></div></dd>';
						/*if(document.getElementById('client_login').value != "client" && document.getElementById('project_status_filter').value == "1") {
							html_body += '<dd class="action" onClick="archiveAlert(' + workorderList[i]['workorders'][e]['id'] + ');">Archive</dd>';
						}
						if(document.getElementById('client_login').value != "client" && document.getElementById('project_status_filter').value == "0") {
							html_body += '<dd class="action" onClick="unarchiveAlert(' + workorderList[i]['workorders'][e]['id'] + ');">Un-Archive</dd>';
						}*/
						html_body += '</dl>';
					}
				}
				//alert(html_body);
				html_bottom += '</div>';
				//alert(html_body);
				if(html_body != ''){
					if(workorderList[i]['client'] != lastComp) {
						html_company_top = '<div class="title_small"><h5>' + workorderList[i]['company_name'] + '</h5></div>';
					}else{
						html_company_top = '';
					}
					html += html_company_top + html_top + html_body + html_bottom;
					lastComp = workorderList[i]['client'];
				}
				html += '<input type=hidden id="active_wo" value=5>'; 
			}
	}
	//Set_Cookie( "lighthouse_wo_data", clientId + '~' + statusId + '~' + assignedTo + '~' + requestTypeFilter + '~' + requestedby , "7", "/", "", "");
	//alert("html"+html);
	$("#wo_containter").html(html);
	wo_loadAssignedList(assignedTo);
	$('#wo_dimmer_ajax').css({display:'none'});
}
function buildWorkordersHTML2() {
	html = "";
    var exp = /((https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    var clientId = '';
    var projectId ='';
    var statusId = '';
    var assignedTo = '';
    var requestedby ='';
    var project_status_filter = '';
    if(document.getElementById("client_filter")){
	clientId = document.getElementById("client_filter").value;
	//projectId = document.getElementById("project_filter").value;
	statusId = document.getElementById("status_filter").value;
	assignedTo = document.getElementById("assigned_filter").value;
	requestedby = document.getElementById("requestedby_filter").value;
	project_status_filter = document.getElementById("project_status_filter").value;
    }
//alert(requestedby);
	// Added this fix for finding previously selected project exists -Chandra
	var isProjectSelectedPresent = 0;
	for (var i = 0; i < workorderList.length; i++) {
		if(workorderList[i]['project_id'] == projectId){
			isProjectSelectedPresent = 1;
			break;
		}
	}
	if(isProjectSelectedPresent == 0 ) {
		projectId = "-1";
	}
	var requestTypeFilter = "";
	if(document.getElementById("requestTypeFilter")!=null)
	{
		requestTypeFilter = document.getElementById("requestTypeFilter").value;
	}
	var assocReqTypeArray = [];
	assocReqTypeArray["Outage"] = "Outage";
	assocReqTypeArray["Problem"] = "Problem";
	assocReqTypeArray["Change"] = "Change";
	var req_txt = "";
	if(requestTypeFilter != null && requestTypeFilter!='' ){
		reqTypeFilter = requestTypeFilter.split(',');
		if(reqTypeFilter.length > 1)
		{
			 assocReqTypeArray = [];
			for (var i = 0; i < reqTypeFilter.length; i++) {
				if(reqTypeFilter[i]!=null && reqTypeFilter[i]!='')
				{
					assocReqTypeArray[reqTypeFilter[i]] = reqTypeFilter[i];
				}
			}
		}
	}
	var statusActiveArray = [];
	if(statusId=='99')
	{
		statusActiveArray["Feedback Provided"] = "Feedback Provided";
		statusActiveArray["On Hold"] = "On Hold";
		statusActiveArray["In Progress"] = "In Progress";
		statusActiveArray["Need More Info"] = "Need More Info";
		statusActiveArray["New"] = "New";
		statusActiveArray["Rejected"] = "Rejected";
		statusActiveArray["Reopened"] = "Reopened";
	}else{
		statusActiveArray[statusId] = statusId;
	}
	
	var project_filter_type = [];
	
	if(project_status_filter == 1){
		//project_filter_type['active'] = 1;
		project_filter_type['archived'] = 0;
	}
	if(project_status_filter == 0){
		//project_filter_type['active'] = 1;
		project_filter_type['archived'] = 1;
	}
	var lastComp;
	for (var i = 0; i < workorderList.length; i++) {
			html_top = '';
			html_body = '';
			html_bottom = '';
			if ((clientId < 0 || clientId == workorderList[i]['client']) && (projectId < 0 || projectId == workorderList[i]['project_id'])){
				if((i-1 >= 0 && workorderList[i-1]['project_name']!= workorderList[i]['project_name']) || i == 0){
					html_top += '<div class="title_small"><h6>' + workorderList[i]['project_code'] + ' - ' + workorderList[i]['project_name'] + '</h6></div>';
			}
				html_top += '<div class="workorders_rows">';
				if(workorderList[i]['workorders'] != null || workorderList[i]['workorders'] != undefined){
				for (var e = 0; e < workorderList[i]['workorders'].length; e++) {
					if (((statusActiveArray[statusId] < 0 || statusActiveArray[workorderList[i]['workorders'][e]['status']] == workorderList[i]['workorders'][e]['status'])|| 
						('over_due' == statusId && '1' == workorderList[i]['workorders'][e]['overdue_flag'])) &&
						(assignedTo < 0 || (assignedTo > 0 && assignedTo == workorderList[i]['workorders'][e]['assigned_to_id'])) && 
						(requestedby < 0 || (requestedby > 0 && requestedby == workorderList[i]['workorders'][e]['requested_by_id']))&& 
						(assocReqTypeArray[workorderList[i]['workorders'][e]['req_type']] == workorderList[i]['workorders'][e]['req_type'])&&
						(project_status_filter > 1 || (project_filter_type['archived'] == workorderList[i]['workorders'][e]['archived'] ))) {
						tClass = workorderList[i]['workorders'][e]['class'];
						html_body += '<dl id="' + workorderList[i]['workorders'][e]['id'] + '" class="' + tClass + '">';

						var wo_id = workorderList[i]['workorders'][e]['id']

						html_body += '<dd class="archivecheck" >';
						html_body += '<input type="checkbox" name="wo_archive_list" value="'+wo_id+'">';
						html_body += '</dd>';

						html_body += '<dd class="id"><a href="/workorders/index/edit/?wo_id=' + workorderList[i]['workorders'][e]['id'] + '">' + wo_id + '</a></dd>';
						html_body += '<dd class="overdue">';
						if(workorderList[i]['workorders'][e]['overdue_flag'] == '1'){
							html_body += '<img src="/_images/flag_icon_red_new.png" title="Over due"/>';
						}
						html_body += '</dd>';
						html_body += '<dt class="title" title="' + workorderList[i]['workorders'][e]['full_title'] + '"><a href="/workorders/index/edit/?wo_id=' + workorderList[i]['workorders'][e]['id'] + '">' + workorderList[i]['workorders'][e]['title'] + '</a></dt>';
						html_body += '<dd class="req_type">' + workorderList[i]['workorders'][e]['req_type'] + '</dd>';
						//html_body += '<dd class="status">' + workorderList[i]['workorders'][e]['status'] + '</dd>';
						html_body += '<dd style="height: 27px;" class="status" id="status' + workorderList[i]['workorders'][e]['id'] + '">';
						html_body += '<span id="status_' + workorderList[i]['workorders'][e]['id'] + '" onClick="woShowStatus(' + workorderList[i]['workorders'][e]['id'] + ',' + workorderList[i]['workorders'][e]['status_id'] + ');">';
						html_body += workorderList[i]['workorders'][e]['status'];
						html_body += '</span><select id="status_select_' + workorderList[i]['workorders'][e]['id'] + '" style="display: none;" onChange="changeStatus(this.value, ' + workorderList[i]['workorders'][e]['id'] + ');">';
						html_body += '</select></dd>';
						html_body += '<dt class="requested">' + workorderList[i]['workorders'][e]['requested_by'] + '</dt>';
						//html_body += '<dd class="assigned"><!-- <a href="">Vorbeck, Garrett</a> --><select><option></option></select></dd>';
						
						
						html_body += '<dd style="height: 27px;" class="assigned" id="assigned_' + workorderList[i]['workorders'][e]['id'] + '">';
						if(workorderList[i]['workorders'][e]['assigned_to'] != null){
							html_body += '<span title="'+workorderList[i]['workorders'][e]['assigned_to']+'" id="assigned_a_' + workorderList[i]['workorders'][e]['id'] + '" onClick="woShowAssigned(' + workorderList[i]['workorders'][e]['id'] + ');">';
							if(workorderList[i]['workorders'][e]['assigned_to'].length > 20){
								var shortText = jQuery.trim(workorderList[i]['workorders'][e]['assigned_to']).substring(0, 20).split(" ").slice(0, 4).join(" ") + "...";
							}else{
								shortText = workorderList[i]['workorders'][e]['assigned_to'];
							}
						}
						html_body += shortText;
						html_body += '</span><select id="assigned_select_' + workorderList[i]['workorders'][e]['id'] + '" style="display: none;" onChange="changeAssigned(this.value, ' + workorderList[i]['workorders'][e]['id'] + ');">';
						html_body += '</select></dd>';
						
						html_body += '<dd class="opendate">' + workorderList[i]['workorders'][e]['open_date'] + '</dd>';

						html_body += '<dd class="due_date">' + workorderList[i]['workorders'][e]['launch_date'] + '</dd>';
	
						if(workorderList[i]['workorders'][e]['wo_last_comment_date']!='N/A' || workorderList[i]['workorders'][e]['wo_last_comment_user'] != 'N/A')
						{					
							html_body += '<dd class="lastcommentby" onmouseover="showComment_wo('+wo_id  +',1);" onmouseout="hideComment_wo('+wo_id  +');">';
							html_body += workorderList[i]['workorders'][e]['wo_last_comment_user'];
							html_body += '</dd>';			
							html_body += '<dd class="commentdate" >';
							html_body += workorderList[i]['workorders'][e]['wo_last_comment_date'];
							html_body += '</dd>';
						}
						else
						{
							html_body += '<dd class="lastcommentby" style="text-align:center;">';
							html_body += workorderList[i]['workorders'][e]['wo_last_comment_user'];
							html_body += '</dd>';			
							html_body += '<dd class="commentdate" style="text-align:center;">';
							html_body += workorderList[i]['workorders'][e]['wo_last_comment_date'];
							html_body += '</dd>';
						}
						var replacedText = (workorderList[i]['workorders'][e]['wo_last_comment']).replace(exp,"<a href='$1' target='_blank'>$1</a>");
						/**
						 * Ticket no 16857,19352
						 */
						//var replacedText = ( unescape(workorderList[i]['workorders'][e]['wo_last_comment']).replace(/\+/g, " ")).replace(exp,"<a href='$1' target='_blank'>$1</a>");
                        //End Ticket
                        html_body += '<dd id ="wo_comment_'+wo_id+'" class="wo_comment" style="display:none;"  onmouseover="showComment_wo('+wo_id  +',3);" onmouseout="hideComment_wo('+wo_id +');"><div class="wo_comment_header"></div><div class="wo_comment_content"><p class="risk_desc">';
						html_body += replacedText;
						html_body += '</p></div><div class="wo_comment_footer"></div></dd>';
						/*if(document.getElementById('client_login').value != "client" && document.getElementById('project_status_filter').value == "1") {
							html_body += '<dd class="action" onClick="archiveAlert(' + workorderList[i]['workorders'][e]['id'] + ');">Archive</dd>';
						}
						if(document.getElementById('client_login').value != "client" && document.getElementById('project_status_filter').value == "0") {
							html_body += '<dd class="action" onClick="unarchiveAlert(' + workorderList[i]['workorders'][e]['id'] + ');">Un-Archive</dd>';
						}*/
						html_body += '</dl>';
					}
				}
				}
				html_bottom += '</div>';
				
				if(html_body != ''){
					if(workorderList[i]['client'] != lastComp) {
						html_company_top = '<div class="title_small"><h5>' + workorderList[i]['company_name'] + '</h5></div>';
					}else{
						html_company_top = '';
					}
					html += html_company_top + html_top + html_body + html_bottom;
					lastComp = workorderList[i]['client'];
				}
				html += '<input type=hidden id="active_wo" value=5>'; 
			}
	}
	//Set_Cookie( "lighthouse_wo_search_data", clientId + '~' + projectId + '~' + statusId + '~' + assignedTo + '~' + requestTypeFilter + '~' + requestedby , "7", "/", "", "");

	$("#wo_containter").html(html);
	wo_loadAssignedList(assignedTo);
	 $('#wo_dimmer_ajax').css({display:'none'});
}

function archiveWO_CheckList(){
	var workorderList = '';
	for (i=0; i<document.getElementsByName('wo_archive_list').length; i++){
		if (document.getElementsByName('wo_archive_list')[i].checked==true)
		{			
			workorderList = workorderList+','+document.getElementsByName('wo_archive_list')[i].value;		
		}
	}	
	if(workorderList!='')
	{    
    $('.message_archive').css({display:'block'});		
		document.getElementById('active_wo').value = workorderList;	
	}else
	{ 
    $('.message_archive_select_check p').html('You need to select the work order to Archive/un-archive.'); 	   
		$('.message_archive_select_check').css({display:'block'});
	}
}

function activeWO_CheckList(){
	var workorderList = '';
	for (i=0; i<document.getElementsByName('wo_archive_list').length; i++){
		if (document.getElementsByName('wo_archive_list')[i].checked==true)
		{			
			workorderList = workorderList+','+document.getElementsByName('wo_archive_list')[i].value;		
		}
	}
	
	if(workorderList!='')
	{	  
		$('.message_active').css({display:'block'});
		document.getElementById('active_wo').value = workorderList;	
	}else
	{
	  $('.message_archive_select_check p').html('You need to select the work order to make it Active.');
		$('.message_archive_select_check').css({display:'block'});
	}
}

function unarchiveWO_CheckList(){

	var workorderList = '';
	for (i=0; i<document.getElementsByName('wo_archive_list').length; i++){
		if (document.getElementsByName('wo_archive_list')[i].checked==true)
		{			
			workorderList = workorderList+','+document.getElementsByName('wo_archive_list')[i].value;		
		}
	}
	
	if(workorderList!='')
	{
		$('.message_unarchive').css({display:'block'});
		document.getElementById('active_wo').value = workorderList;	
	}else
	{
		$('.message_archive_select_check').css({display:'block'});
	}
}

function showComment_wo(wo_id,flag){
	var topVar = ($('#wo_comment_'+wo_id).offset().top + 58);	
	if(flag==1)
	{
		var leftVar = 630;	
		$('#wo_comment_'+wo_id).css({display:'block', left: leftVar + 'px'});
	}
	else if (flag==2)
	{
		var leftVar = 720;	
		$('#wo_comment_'+wo_id).css({display:'block',left: leftVar + 'px'});
	}
    else
	{
		$('#wo_comment_'+wo_id).css({display:'block'});
	}
}

function hideComment_wo(wo_id){
	$('#wo_comment_'+wo_id).css({display:'none'});
}

function sortWorkorders(sortType){	
	if (currentSort == sortType) {
		if (sortDir == 1) {
			sortDir = 0;
		} else {
			sortDir = 1;
		} 
	}
  if($('#project_status_filter').val() == '0'){
    if(sortType == 'lastcommentby' || sortType == 'commentdate' ){
    } else {
      $('#wo_dimmer_ajax').css({display:'block'});
      displayWorkorders('1','first',sortType,sortDir);
    if (sortDir == 1) {
      $("#" + sortType + "sort").addClass("up").removeClass("down");
    } else {
      $("#" + sortType + "sort").removeClass("up").addClass("down");
    }
    currentSort = sortType;
    }
  } else {
  	for (var i = 0; i < workorderList.length; i++) {
  		tList = workorderList[i]['workorders'];
  		switch (sortType) {
  			case "id":
  				tList.sort(sortById);
  				break;
  			case "title":
  				tList.sort(sortByTitle);
  				break;
  			case "req_type":
  				tList.sort(sortByReqType);
  				break;
  			case "status":
  				tList.sort(sortByStatus);
  				break;
  			case "requested_by":
  				tList.sort(sortByRequestedBy);
  				break;
  			case "assigned_to":
  				tList.sort(sortByAssignedTo);
  				break;
  			case "open_date":
  				tList.sort(sortByOpenDate);
  				break;
  			case "due_date":
  				tList.sort(sortByDueDate);
  				break;
  			case "lastcommentby":
  				tList.sort(sortByLastCommentBy);
  				break;
  			case "commentdate":
  				tList.sort(sortByCommentDate);
  				break;
  			default:
  		}
  		if (sortDir == 1) {
  			tList.reverse();
  		}
  		workorderList[i]['workorders'] = tList;
  	}
  	
  	$(".workorders_sort li a").removeClass("down").removeClass("up");
  	
  	if (sortDir == 1) {
  		$("#" + sortType + "sort").addClass("up").removeClass("down");
  	} else {
  		$("#" + sortType + "sort").removeClass("up").addClass("down");
  	}
  
  	currentSort = sortType;		
  	displayWorkorders();
  }
}

//*************************
//**Custom sort functions**
//*************************	
function sortByTitle(a, b) {
    var x = a["title"].toLowerCase();
    var y = b["title"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
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

function sortByAssignedTo(a, b) {
    var x = a["assigned_to"].toLowerCase();
    var y = b["assigned_to"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByRequestedBy(a, b) {
    var x = a["requested_by"].toLowerCase();
    var y = b["requested_by"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByOpenDate(a, b) {
    var x = a["open_date"].toLowerCase();
    var y = b["open_date"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByCompleted(a, b) {
    var x = a["completed"].toLowerCase();
    var y = b["completed"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByAssignedDate(a, b) {
    var x = a["assigned_date"].toLowerCase();
    var y = b["assigned_date"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByBudget(a, b) {
    var x = a["budget"].toLowerCase();
    var y = b["budget"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}
function sortById(a, b) {
    var x = parseInt(a["id"]);
    var y = parseInt(b["id"]);
	return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByDueDate(a, b) {
    var x = a["launch_date"].toLowerCase();
    var y = b["launch_date"].toLowerCase();
	return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByLastCommentBy(a, b) {
    var x = a["wo_last_comment_user"].toLowerCase();
    var y = b["wo_last_comment_user"].toLowerCase();
	return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByCommentDate(a, b) {
    var x = a["wo_last_comment_date"].toLowerCase();
    var y = b["wo_last_comment_date"].toLowerCase();
	return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function generateWOReport(){
	var woLists = $('#woLists').val();	
	$.download('/_ajaxphp/search.php','report=excel&status='+$("#project_status_filter").val()+'&client='+$("#client_filter").val()+'&proj_id='+$("#project_filter").val()+'&status_filter='+$("#status_filter").val()+'&assigned_to='+$("#assigned_filter").val()+'&req_type='+$("#requestTypeFilter").val()+'&idess='+woLists );
  
}

function gotoWorkorder(){
	var woId = document.getElementById("wo_id").value;
	if(woId == "" || woId == "id #") {
		alert("Please enter a Workorder ID");
	} else {
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/wo_exist_check.php?woId="+woId,
			success: function(msg) {
				if(msg == "1"){
					window.location = "/workorders/index/edit/?wo_id="+woId;
				}else{
					alert("Work order does not exist.");
				}
			}
		});
	}
	return false;
}

function timeSpan(fromDate, toDate, interval){
   var second=1000, minute=second*60, hour=minute*60, day=hour*24, week=day*7;
    date1 = new Date(fromDate);
    date2 = new Date(toDate);
 
    var timediff = date2 - date1;
    if (isNaN(timediff)) return NaN;
    switch (interval) {
        case "years": return date2.getFullYear() - date1.getFullYear();
        case "months": return (
            ( date2.getFullYear() * 12 + date2.getMonth() )
            -
            ( date1.getFullYear() * 12 + date1.getMonth() )
        );
        case "weeks"  : return Math.floor(timediff / week);
        case "days"   : return Math.floor(timediff / day); 
        case "hours"  : return Math.floor(timediff / hour); 
        case "minutes": return Math.floor(timediff / minute);
        case "seconds": return Math.floor(timediff / second);
        default: return undefined;
    }
 
}

//http://filamentgroup.com/lab/jquery_plugin_for_requesting_ajax_like_file_downloads/
jQuery.download = function(url, data, method){
	//url and data options required
	if( url && data ){ 
		//data can be string of parameters or array/object
		data = typeof data == 'string' ? data : jQuery.param(data);
		//split params into form inputs
		var inputs = '';
		jQuery.each(data.split('&'), function(){ 
			var pair = this.split('=');
			inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
		});
		//send request
		jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
		.appendTo('body').submit().remove();
	};
};

