$(document).ready(function() {

	if(document.getElementById('start_date').value != "") {
		var wo_id = document.getElementById('defect_id').value;
	}

	if($('#wo_assigned_user').val()!='')
	{
		changeImage($('#wo_assigned_user').val());
	}
	
	updateComments();
	updateFileList();
	$('#save_buttons_dimmer').css({display:'none'});
});

function showHideTime() {
  
}
  
function getRequestorsInfo(userId) {
	qString = '?action=entry&data=phone&user='+userId;
	qString2 = '?action=entry&data=email&user='+userId;
	qString3 = '?action=entry&data=name&user='+userId;
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_roles.php"+qString,
		success: function(msg) {
			document.getElementById('requestor_phone').value = msg;
		}
	});
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_roles.php"+qString2,
		success: function(msg) {
			document.getElementById('requestor_email').value = msg;
		}
	});
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_roles.php"+qString3,
		success: function(msg) {
			document.getElementById('requestor_name').value = msg;
		}
	});
}

function updateVersion(project_Id) {
	qString = '?project_id='+project_Id;

	updateIteration(project_Id);
	updateProduct(project_Id);	
	jQuery.getJSON('/_ajaxphp/qa_update_version.php'+qString, function(json) {
			var select = document.getElementById("QA_VERSION");
			select.length = 0;
			select.options[0] = new Option("--Select Version--","_blank"); 
			for(var i=0;i<json.length;i++)
			{
				select.options[i+1] = new Option(json[i]['version_name'],json[i]['id']); 
			}
		});
}
function updateProduct(project_Id) {
	qString = '?project_id='+project_Id;
		
	jQuery.getJSON('/_ajaxphp/qa_update_product.php'+qString, function(json) {
			var select = document.getElementById("QA_PRODUCT");
			select.length = 0;
			select.options[0] = new Option("--Select Product--","_blank"); 
			for(var i=0;i<json.length;i++)
			{
				select.options[i+1] = new Option(json[i]['product_name'],json[i]['id']); 
			}
		});
}
function updateIteration(project_Id) {
	qString = '?project_id='+project_Id;
		
	jQuery.getJSON('/_ajaxphp/qa_update_iteration.php'+qString, function(json) {
			var select = document.getElementById("QA_ITERATION");
			select.length = 0;
			select.options[0] = new Option("--Select Iteration--","_blank"); 
			for(var i=0;i<json.length;i++)
			{
				select.options[i+1] = new Option(json[i]['iteration_name'],json[i]['id']); 
			}
		});
}
function saveWorkOrderConfirm()
{

	$('.message_outage_submit').css({display:'block'});
}


function qaStatusChange(qaStatus)
{
	if(qaStatus !='' && qaStatus!='1' && qaStatus!='2' && qaStatus!='7' && qaStatus!='8')
	{
		$('#wo_assigned_user').attr('selectedIndex', 0);
	}	
}

function getCurrentDate() {
	var currentDate = new Date();
	
	var month = currentDate.getMonth()+1;
	var day = currentDate.getDate();
	var year = currentDate.getFullYear();
	
	var dt = month+'/'+day+'/'+year;
	
	return dt;
}

function dateExists(date, month, year){
    var d = new Date(year, month, date);
    return d.getDate() === parseInt(date); //parseInt makes sure it's an integer.
}

function saveWorkOrder(savebtn) {
	var qString = "";
	var valid = true;
	
	//wo_dimmer
	//wo_dimmer_ajax
	
	var defectID = document.getElementById('defect_id').value;
	var dirName = document.getElementById('dirName').value;
	var requestedId = document.getElementById('wo_requested_by').value;
	var projectId = document.getElementById('wo_project').value;

	var woTypeId = "";
	var priorityId = '';//document.getElementById('wo_priority').value;
	var timeSens = "true";//document.getElementById('time_sensitive').checked;
	
	var woTitle = document.getElementById('wo_title').value;
	var woExampleURL = document.getElementById('wo_example_url').value;
	var woDesc = document.getElementById('wo_desc').value;
	var woAssignedTo = document.getElementById('wo_assigned_user').value;
	var woStatus = document.getElementById('wo_status').value;
	var woStartDate = document.getElementById('start_date').value;

	var qaCATEGORY = document.getElementById('QA_CATEGORY').value;
	var qaSEVERITY = document.getElementById('QA_SEVERITY').value;
	var qaOS = document.getElementById('QA_OS').value;
	var qaBROWSER = document.getElementById('QA_BROWSER').value;
	var qaVERSION = document.getElementById('QA_VERSION').value;
	var qaDETECTED_BY = document.getElementById('QA_DETECTED_BY').value;
	var qaORIGIN = document.getElementById('QA_ORIGIN').value;
    var qaCCList = document.getElementById('cclist').value;
    //LH28522
    var qaITERATION = document.getElementById('QA_ITERATION').value;
    var qaPRODUCT = document.getElementById('QA_PRODUCT').value;
	$('.message_outage_submit').css({display:'none'});		
	
	var comment = document.getElementById('comment').value;
	qaLogUsedID = $('#user_id').val();
	
	var qa_assigned_to_user = $('#qa_assigned_to_user').val();
	var qa_current_status = $('#qa_current_status').val();

	if((qa_current_status =='6'||qa_current_status =='1' ||qa_current_status =='10') && comment != "" && qaLogUsedID==qa_assigned_to_user && (qa_current_status==woStatus))
	{		
		if(qa_current_status =='6' || qa_current_status =='10')
		{
			$('#wo_assigned_user').attr('selectedIndex', 0);
		}
		$('#wo_status').attr('selectedIndex', 0);
		
		woAssignedTo = $('#wo_assigned_user').val();
		woStatus = $('#wo_status').val();
	}

	if(qa_current_status =='8' && savebtn == 'reopen' )
	{
		woStatus = '5';
		$("#wo_status option[value='"+woStatus+"']").attr('selected', 'selected');
		if(qa_assigned_to_user==woAssignedTo)
		{
			$('#wo_assigned_user').attr('selectedIndex', 0);
			woAssignedTo = $('#wo_assigned_user').val();
		}	
		savebtn =='update';		
	}

	if(defectID != "")
	{  
		if(woStatus == "") {
			valid = false;
			document.getElementById('qa_status_label').style.color = "#FF0000";
		} else {
			document.getElementById('qa_status_label').style.color = "#000000";
		}
	}

	if(projectId == "") {
		valid = false;
		document.getElementById('wo_project_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_project_label').style.color = "#000000";
	}

	if(qaCATEGORY == "_blank") {
		valid = false;
		document.getElementById('wo_qa_category_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_qa_category_label').style.color = "#34556C";
	}

	if(woAssignedTo == "") {
		valid = false;
		document.getElementById('wo_assigned_user_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_assigned_user_label').style.color = "#34556C";
	}

	if(qaSEVERITY == "_blank") {
		valid = false;
		document.getElementById('wo_qa_severity_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_qa_severity_label').style.color = "#34556C";
	}

	if(woTitle == "") {
		valid = false;
		document.getElementById('wo_title_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_title_label').style.color = "#34556C";
	}
	if(woDesc == "") {
		valid = false;
		document.getElementById('wo_desc_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_desc_label').style.color = "#34556C";
	}
	
	if(qaORIGIN == "_blank") {
		valid = false;
		document.getElementById('QA_ORIGIN_label').style.color = "#FF0000";
	} else {
		document.getElementById('QA_ORIGIN_label').style.color = "#34556C";
	}

	if(qaOS == "_blank") {
		valid = false;
		document.getElementById('QA_OS_label').style.color = "#FF0000";
	} else {
		document.getElementById('QA_OS_label').style.color = "#34556C";
	}

	if(qaBROWSER == "_blank") {
		valid = false;
		document.getElementById('QA_BROWSER_label').style.color = "#FF0000";
	} else {
		document.getElementById('QA_BROWSER_label').style.color = "#34556C";
	}

	if(qaVERSION == "_blank") {
		valid = false;
		document.getElementById('QA_VERSION_label').style.color = "#FF0000";
	} else {
		document.getElementById('QA_VERSION_label').style.color = "#34556C";
	}
	
	if(qaDETECTED_BY == "_blank") {
		valid = false;
		document.getElementById('QA_DETECTED_BY_label').style.color = "#FF0000";
	} else {
		document.getElementById('QA_DETECTED_BY_label').style.color = "#34556C";
	}
	
	if(valid) {
		data = {defectID:defectID,dirName:dirName,requestedId:requestedId,projectId:projectId,woTypeId:woTypeId,priorityId:priorityId,timeSens:timeSens,woTitle:woTitle,woExampleURL:woExampleURL,woDesc:woDesc,woStatus:woStatus,woAssignedTo:woAssignedTo,woStartDate:woStartDate,qaCATEGORY:qaCATEGORY,qaSEVERITY:qaSEVERITY,qaOS:qaOS,qaBROWSER:qaBROWSER,qaVERSION:qaVERSION,qaDETECTED_BY:qaDETECTED_BY,qaORIGIN:qaORIGIN,qaCCList:qaCCList,qaITERATION:qaITERATION,qaPRODUCT:qaPRODUCT};
		$('#wo_dimmer').css({display:'block'});
		$('#wo_dimmer').css({backgroundColor:'#FFFFFF'});
		$('#wo_dimmer').css({opacity:'0.7'});
		$('#wo_dimmer').css({filter:'0.7'});
		$('#wo_dimmer_ajax').css({display:'block'});
		
		$.ajax({
			type: "POST",
			url: "/_ajaxphp/quality_wo_save.php",
			data: data,
			success: function(msg) {

				$('#wo_dimmer').css({display:'none'});
				$('#wo_dimmer_ajax').css({display:'none'});

				if(parseInt(msg)) {
					$('#defect_id').val(msg);
					$('#dirName').val(msg+"/");				
					
					if($('#defect_id').val() != "") {
						$('#qa_comment_dimmer').css({display:"none"});
					}
					
				if(savebtn == 'save')
				{
					Set_Cookie( "lighthouse_quality_create_defect", projectId, "7", "/", "", "");
					Set_Cookie( "lighthouse_quality_list", projectId , "7", "/", "", "");
					Set_Cookie( "lh_qa_project_cookie", projectId , "7", "/", "", "");
					$('.message_required p').html('New defect created successfully.');
					$('.message_required').css({display:'block'});
					$('.message_required button').click(function(){

							window.location = '/quality/'; 
					});
				}
				else
				{
					var qa_project_remember = document.getElementById('qa_project_remember');
					var qa_category_remember = document.getElementById('qa_category_remember');
					var qa_severity_remember = document.getElementById('qa_severity_remember');
					var qa_assigned_remember = document.getElementById('qa_assigned_remember');
					var qa_exampleurl_remember = document.getElementById('qa_exampleurl_remember');
					var qa_origin_remember = document.getElementById('qa_origin_remember');
					var qa_os_remember = document.getElementById('qa_os_remember');
					var qa_browser_remember = document.getElementById('qa_browser_remember');
					var qa_version_remember = document.getElementById('qa_version_remember');
					var qa_iteration_remember = document.getElementById('qa_iteration_remember');
					var qa_product_remember = document.getElementById('qa_product_remember');
					var cookie_value = '';
					$('#qa_assigned_to_user').val(woAssignedTo);
					$('#qa_current_status').val(woStatus);
					if(qa_project_remember.checked == true)
					{
						cookie_value = cookie_value+'P:'+projectId;
					}
					else
					{
						cookie_value = cookie_value+'P:';
					}
					if(qa_category_remember.checked == true)
					{
						cookie_value = cookie_value+'~C:'+qaCATEGORY;
					}
					else
					{
						cookie_value = cookie_value+'~C:';
					}
					if(qa_severity_remember.checked == true)
					{
						cookie_value = cookie_value+'~S:'+qaSEVERITY;
					}
					else
					{
						cookie_value = cookie_value+'~S:';
					}
					if(qa_assigned_remember.checked == true)
					{
						cookie_value = cookie_value+'~A:'+woAssignedTo;
					}
					else
					{
						cookie_value = cookie_value+'~A:';
					}
					if(qa_exampleurl_remember.checked == true)
					{
						cookie_value = cookie_value+'~URL:'+woExampleURL;
					}
					else
					{
						cookie_value = cookie_value+'~';
					}
					if(qa_origin_remember.checked == true)
					{
						cookie_value = cookie_value+'~O:'+qaORIGIN;
					}
					else
					{
						cookie_value = cookie_value+'~O:';
					}
					if(qa_os_remember.checked == true)
					{
						cookie_value = cookie_value+'~OS:'+qaOS;
					}
					else
					{
						cookie_value = cookie_value+'~OS:';
					}
					if(qa_browser_remember.checked == true)
					{
						cookie_value = cookie_value+'~B:'+qaBROWSER;
					}
					else
					{
						cookie_value = cookie_value+'~B:';
					}
					//LH28522
					if(qa_iteration_remember.checked == true)
					{
						cookie_value = cookie_value+'~IT:'+qaITERATION;
					}
					else
					{
						cookie_value = cookie_value+'~IT:';
					}
					if(qa_product_remember.checked == true)
					{
						cookie_value = cookie_value+'~PR:'+qaPRODUCT;
					}
					else
					{
						cookie_value = cookie_value+'~PR:';
					}
					//END
					if(qa_version_remember.checked == true)
					{
						cookie_value = cookie_value+'~V:'+qaVERSION;
					}
					else
					{
						cookie_value = cookie_value+'~V:';
					}
					
					Set_Cookie( "lighthouse_quality_create_defect", cookie_value, "7", "/", "", "");
					Set_Cookie( "lh_qa_project_cookie", projectId , "7", "/", "", "");
					$('.message_required p').html('The defect is saved successfully.');
					$('.message_required').css({display:'block'});
					if(savebtn =='update'){	
						updateStatusList(defectID,woStatus);
						submitCommentANDsave();
					}
					if(savebtn =='save&add'){	
						$('.message_required button').click(function(){
							window.location = '/quality/index/create/';
						});
					}
					if(woStatus == '5'){	
						$('.message_required button').click(function(){
							window.location = '/quality/';
						});
					}





				}
					
				}
			},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
						alert("Error:" + textStatus + msg2 );
				}		
		});
				
	} else {
		$('.message_required p').html('Please fill in the required fields?');
		$('.message_required').css({display:'block'});
	}
}

function updateStatusList(defectID,qaStatus)
{
	qString = '?defectID='+defectID+'&qaStatus='+qaStatus;
		
	jQuery.getJSON('/_ajaxphp/qa_fetch_status_list.php'+qString, function(json) {
			var select = document.getElementById("wo_status");
			select.length = 0;
			select.options[0] = new Option("--Select Type--",""); 
			for(var i=0;i<json.length;i++)
			{
				if(qaStatus==json[i]['id'])
				{
					select.options[i+1] = new Option(json[i]['name'],json[i]['id'],true); 
				}
				else
				{
					select.options[i+1] = new Option(json[i]['name'],json[i]['id']); 
				}
				
			}
			$("#wo_status option[value='"+qaStatus+"']").attr('selected', 'selected');

		});


}

function updateComments() {
	var qString2 = "defect_id="+document.getElementById('defect_id').value;
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/get_num_qa_comments.php",
		data: qString2,
		success: function(msg) {
			$("#number_of_comments").html("Comments ("+msg+")");
		}
	});
}

function closeDefect() {
	$('.message_close').css({display:'none'});
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/close_qa.php",
		data: "defectId="+document.getElementById('defect_id').value,
		success: function(msg) {
			document.getElementById('close_date').value=msg;
			window.location = '/quality/';
		}
	});
}

function checkDirName(fileForm) {
	var dirName = document.getElementById('dirName');
	var woid = document.getElementById('defect_id').value;
	var fileList = document.getElementById('file_upload_list');
	
	$('#file_upload_dimmer').css({display:'block'});
	$('#save_buttons_dimmer').css({display:'block'});
	$('#file_upload_dimmer').css({backgroundColor:'#FFFFFF'});
	$('#file_upload_dimmer').css({opacity:'0.7'});
	$('#file_upload_dimmer').css({filter:'0.7'});
	$('#save_buttons_dimmer').css({backgroundColor:'#FFFFFF'});
	$('#save_buttons_dimmer').css({opacity:'0.7'});
	$('#save_buttons_dimmer').css({filter:'0.7'});
	$('#file_upload_ajax').css({display:'block'});	

	$.ajax({
			type: "GET",
			url: "/_ajaxphp/qa_create_file_dir.php?dirName="+dirName.value,
			success: function(msg) {
				if(dirName.value == "") {
					dirName.value = msg;
				}				
				ajaxUploadFile(dirName.value,woid);
				fileForm.upload_file.value="";
			}
	});

}

function updateFileList() {
	var dirName = document.getElementById('dirName');
	var fileList = document.getElementById('file_upload_list');
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/qa_file_listing.php?dirname="+dirName.value,
		success: function(msg) {
			fileList.innerHTML = msg;
			$('#file_upload_dimmer').css({display:'none'});
			$('#save_buttons_dimmer').css({display:'none'});
			$('#file_upload_ajax').css({display:'none'});
		}
	});  
}

function submitFileForm() {
	document.file_upload_form.submit();
	updateFileList(); 
}

function removeFile(fileNum) {
	//Remove this file from the database and repopulate the list
	//$("#file_" + fileNum)
	//alert('removing file_'+fileNum);

		var html = '<div class="file_message_confirm">';
		html +=	'<p> Are you sure you would like to remove the attachment? </p>';
		html +=	'<div style="clear: both;"></div>';
		html +=	'<div class="duplicate_buttons">';
		html +=	'	<button onClick="removeFileConfirmed('+fileNum+');"><span>Yes</span></button>';
		html +=	'	<button class="cancel" onClick="$(\'.file_message_confirm\').css({display:\'none\'}); return false;"><span>No</span></button>';
		html +=	'	<div style="clear: both;"></div>';
		html +=	'</div>';
		html +=	'</div>';
	$('body').append(html);
	$('.file_message_confirm').css({display:'block'});
	

}

function removeFileConfirmed(fileNum) {
	$.ajax({ 
		type: "GET",
		url: "/_ajaxphp/qa_remove_uploaded_file.php",
		data: "id="+fileNum,
		success: function(msg) {
			//alert("Data Saved: " + msg)
		}
	});
	$('.file_message_confirm').css({display:'none'});	
	updateFileList();
}

function uploadFile(theForm) {
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/uploadFile.php",
		data: "file="+theForm.upload_file.value,
		success: function(msg) {
			//alert("Data Saved: " + msg)
		}
	});
	
	updateFileList();
	
	$("#upload_file").value = "";
}

function changeImage(userID)
{
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/getUserImage.php",
		data: "userID="+userID,
		success: function(msg) {
			$('#assigned_user_img').attr('src',msg);
		}
	});
}  

function submitComment() {
	
	qaStatus = $('#wo_status').val();		

	var defectId = document.getElementById('defect_id').value;
	var userId = document.getElementById('user_id').value;
	var comment = document.getElementById('comment').value;


	var qa_assigned_to_user = $('#qa_assigned_to_user').val();
	var qa_current_status = $('#qa_current_status').val();


	qaLogUsedID = $('#user_id').val();
	qaAssignedUsedID = $('#wo_assigned_user').val();
	
	if((qa_current_status =='6'||qa_current_status =='1' ||qa_current_status =='10') && $.trim(comment) != "" && qaLogUsedID==qaAssignedUsedID && (qa_current_status==qaStatus))
	{			
		saveWorkOrder('update');
		return false;
	}
	if($.trim(comment) != "" && userId != "" && defectId != "") {
		// Save workorder first and then save the comment it calls submitCommentANDsave function
		saveWorkOrder('update');
	}	
	return false;
}


function submitCommentANDsave() {

	var defectId = document.getElementById('defect_id').value;
	var userId = document.getElementById('user_id').value;
	var comment = document.getElementById('comment').value;

	if(comment != "" && userId != "" && defectId != "") {
		
		$.ajax({
			type: "POST",
			async: false,
			url: "/_ajaxphp/qa_save_comment.php",
			data: { defectId : defectId, userId : userId, comment : comment},
			success: function(msg) {
				$('#comments_list').html(msg) ;
				document.getElementById('comment').value = "";
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
			    alert("Error:" + textStatus); 
			}		
		});
	}	
	updateComments();	
	return false;
}

//added for 18088

function updateCClist(Project_id){
if(Project_id != ''){
	$.ajax({
				type: "GET",
				url: "/_ajaxphp/qa_projectcclist.php",
				data: "project_id="+Project_id,
				success: function(msg) {
					if(document.getElementById('cc_list')){
					document.getElementById('cc_list').innerHTML = msg;
					
					$('#cclist').val($('#temp_cc_list').val());	
					}

				}
			});
			
		}
}

$(document).ready(function() {
	var defectId = $('#defect_id').val();
	if(defectId == ''){
	var Project_id = $('#wo_project').val();
		if(Project_id != ''){
		$.ajax({
				type: "GET",
				url: "/_ajaxphp/qa_projectcclist.php",
				data: "project_id="+Project_id,
				success: function(msg) {
					$('#cc_list').html(msg);
					
					$('#cclist').val($('#temp_cc_list').val());	

				}
			});
		}
	}	

});

function addCcUser() {
	var defectId = document.getElementById('defect_id').value;
	var cc = document.getElementById('cc_user').value;
	var project_id=document.getElementById('wo_project').value;	
	document.getElementById('cclist').value += cc +",";
	
	var cclist = document.getElementById('cclist').value;

	//if(defectId != "") {
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/qa_save_cc.php",
			//data: "defectId="+defectId+"&cc="+cclist,
			//data: "defectId="+defectId+"&cc="+cclist+"&addCC="+cc,
			 data: "defectId="+defectId+"&cc="+cclist+"&addCC="+cc+"&project_id="+project_id, 
			success: function(msg) {
				document.getElementById('cc_list').innerHTML = msg;
			}
		});
	//}
}

function removeCcUser(userId) {
	var defectId = document.getElementById('defect_id').value;
	var cclist = document.getElementById('cclist').value;
        //LH Defect 3935
	if(cclist!=null)
	{
               // alert("test");
		var col_array= cclist.split(',');
		var part_num=0;
		var finalccList = "";

		while (part_num < col_array.length)
		{

			if(col_array[part_num]!=userId && col_array[part_num] !='')
			{
			  finalccList = finalccList + col_array[part_num] +',';
			}

		    part_num+=1;
		}
	}
        document.getElementById('cclist').value = finalccList; 
        //End
	if(defectId != "") {
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/qa_save_cc.php",
			data: "defectId="+defectId+"&cc="+cclist+"&remove="+userId,
			success: function(msg) {
				document.getElementById('cc_list').innerHTML = msg;
			}
		});
	}
}

function ajaxUploadFile(dirName, woId){
	/*
	prepareing ajax file upload
	url: the url of script file handling the uploaded files
	fileElementId: the file type of input element id and it will be the index of $_FILES Array()
	dataType: it support json, xml
	secureuri:use secure protocol
	success: call back function when the ajax complete
	error: callback function when the ajax failed

	*/
	$.ajaxFileUpload
	(
		{
			url:'/_ajaxphp/qa_uploadFile.php',
			secureuri:false,
			fileElementId:'upload_file',
			dataType: 'text',
			data:'dirName='+dirName+'&defect_id='+woId,
			success: function (data, status)
			{
				if(data.search("success")>='0'){
					updateFileList();
					$('.message_required p').html('File uploaded successfully.');
					$('.message_required').css({display:'block'});
				}
				else if(data.search("You")>='0'){
					updateFileList();
					$('.message_required p').html('File extention not supported.');
					$('.message_required').css({display:'block'});
				}
				else if(data.search("Exdeed")>='0'){
					updateFileList();
					$('.message_required p').html('File cant be uploaded, size is more then 10 MB.');
					$('.message_required').css({display:'block'});
				}
				else{
					$('.message_required p').html('Error in uploading the file. Please check the size of the file and upload again.');
					$('.message_required').css({display:'block'});
					$('#file_upload_dimmer').css({display:'none'});
					$('#save_buttons_dimmer').css({display:'none'});
					$('#file_upload_ajax').css({display:'none'});
				}
			},
			error: function (data, status, e)
			{
				$('.message_required p').html('Error in uploading the file. Please check the size of the file and upload again.');
				$('.message_required').css({display:'block'});
				$('#file_upload_dimmer').css({display:'none'});
				$('#save_buttons_dimmer').css({display:'none'});
				$('#file_upload_ajax').css({display:'none'});
			}
		}
	)
	return false;
}

function reOpenWorkOrder() {
	$('.message_reopen').css({display:'none'});
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/reopen_qa.php",
		data: "defectId="+document.getElementById('defect_id').value,
		success: function(msg) {
			document.getElementById('close_date').value=msg;
			window.location = '/quality/';
		}
	});
}

function navigate(page){
  
  var form = document.getElementById('prevNextNav');
  form.action = '/quality/index/edit/?defect_id='+page;
  form.submit();  
}

function qualityeditsearch(){ 
	var defectId = document.getElementById("defect_search_id").value;
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

