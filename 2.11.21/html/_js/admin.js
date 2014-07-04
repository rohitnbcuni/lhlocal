
$(document).ready(function(){
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
	
	var adminTitlemsg = $('#adminTitlemsg').val();
	
	if(adminTitlemsg=='' || adminTitlemsg=='User Info')
	{
		adminTitlemsg = 'User Info';
		$('#create_sections li').removeClass('active');
		$('#create_sections #fetchUser').addClass('active');
	}
	else
	if(adminTitlemsg == 'Project Versions')
	{
		$('#create_sections li').removeClass('active');
		$('#create_sections #fetchProjVersion').addClass('active');
	}
	else if(adminTitlemsg == 'Default CC List')
	{
		$('#create_sections li').removeClass('active');
		$('#create_sections #projectDefaultCC').addClass('active');
	}
	else if(adminTitlemsg == 'Quality Default CC List')
	{
		$('#create_sections li').removeClass('active');
		$('#create_sections #QCprojectDefaultCC').addClass('active');
	}
	else if(adminTitlemsg == 'Work order SLA Report')
	{
		$('#create_sections li').removeClass('active');
		$('#create_sections #workorderSLAReport').addClass('active');
	}
	else if(adminTitlemsg == 'Site Names')
	{
		$('#create_sections li').removeClass('active');
		$('#create_sections #workorderSiteName').addClass('active');
	}
	else if(adminTitlemsg == 'Custom Fields')
	{
		$('#create_sections li').removeClass('active');
		$('#create_sections #customFieldName').addClass('active');
	}else if(adminTitlemsg == 'Project Products')
	{
		$('#create_sections li').removeClass('active');
		$('#create_sections #fetchProjProduct').addClass('active');
	}else if(adminTitlemsg == 'WO Default CC List')
	{
		$('#create_sections li').removeClass('active');
		$('#create_sections #projectDefaultCC').addClass('active');
	}else if(adminTitlemsg == 'Project Iterations')
	{
		$('#create_sections li').removeClass('active');
		$('#create_sections #fetchProjIteration').addClass('active');
	}else if(adminTitlemsg == 'Rally Lighthouse Mapping')
	{
		$('#create_sections li').removeClass('active');
		$('#create_sections #rallyLHProjects').addClass('active');
	}
	else if(adminTitlemsg == 'Solr Search Log')
	{
		$('#create_sections li').removeClass('active');
		$('#create_sections #searchLHProjects').addClass('active');
	}
	else if(adminTitlemsg == 'Basecamp Lighthouse Mapping')
	{
		$('#create_sections li').removeClass('active');
		$('#create_sections #lhBasecamp').addClass('active');
	}
	$('.title_lrg h4').html(adminTitlemsg);
	
	
	
});


$(document).ready(function(){
	//$(".ui-multiselect").removeClass('button');
	if($("#userProjectArray")){
		$("#userProjectArray").multiselect({create: function(){ $(this).next().width(200); }}).multiselectfilter();
	
	}
	if($("#admin_requested_select")){
		$("#admin_requested_select").multiselect({selectedText:"# Selected Users"},{create: function(){ $(this).next().width(200); }}).multiselectfilter();
	
	}
	
		
	});

//http://filamentgroup.com/lab/jquery_plugin_for_requesting_ajax_like_file_downloads/

	
function createSolrLog(){
		window.open('/_ajaxphp/solr_search_log.php');
	}
function editUser() {
	$(".adminCheckBox").removeAttr('disabled');
	$('#admin_UserTitle_fade').css({display:'none'});
	$('#admin_UserStatus_fade').css({display:'none'});
	$('#updateButton').css({display:'block'});
	$('#editButton').css({display:'none'});
}
function addCcUser(projectId) {
	var cc = document.getElementById('cc_user').value;
	
	if(endsWith(cc,",")){
    	 document.getElementById('cclist').value += cc +",";
	}else{
        document.getElementById('cclist').value += ","+cc +",";
	} 
	
	var cclist = document.getElementById('cclist').value;
	//alert('add cc: '+woId+":"+cc);

	$.ajax({
		type: "GET",
		url: "/_ajaxphp/add_default_cc.php",
		data: "projectId="+projectId+"&cc="+cclist,
		success: function(msg) {
			document.getElementById('cc_list').innerHTML = msg;
		}
	});
}

function addDefaultCompany(projectId) {
	var cclist = document.getElementById('cclist').value;
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/add_default_cc_company.php",
		data: "projectId="+projectId+"&cc="+cclist,
		success: function(msg) {
			$('.message_required p').html('Updated Successfully.');
			$('.message_required').css({display:'block'});
		}
	});

}
////////////18474/////////
function addCcUserQC(projectId) {
	var cc = document.getElementById('qccc_user').value;

	if(endsWith(cc,",")){
    	 document.getElementById('qccclist').value += cc +",";
	}else{
        document.getElementById('qccclist').value += ","+cc +",";
	} 
	
	var cclist = document.getElementById('qccclist').value;
	//alert('add cc: '+cclist+":"+cc);

	$.ajax({
		type: "GET",
		url: "/_ajaxphp/add_QCdefault_cc.php",
		data: "projectId="+projectId+"&cc="+cclist,
		success: function(msg) {
			document.getElementById('qccc_list').innerHTML = msg;
		}
	});
}



function addQCDefaultCompany(projectId) {
	var cclist = document.getElementById('qccclist').value;
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/add_QCdefault_cc_company.php",
		data: "projectId="+projectId+"&cc="+cclist,
		success: function(msg) {
			$('.message_required p').html('Updated Successfully.');
			$('.message_required').css({display:'block'});
		}
	});

}

function removeqcCcUser(userId,projectId) {
	//var cc = document.getElementById('cc_user').value;
	
	//document.getElementById('cclist').value += cc +",";


	var cclist = document.getElementById('qccclist').value;
	if(cclist!=null)
	{
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
	document.getElementById('qccclist').value = finalccList;
	//alert('add cc: '+woId+":"+cc);
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/add_QCdefault_cc.php",
		data: "projectId="+projectId+"&cc="+cclist+"&remove="+userId,
		success: function(msg) {
			document.getElementById('qccc_list').innerHTML = msg;
		}
	});

	return false;
}
///////////////////////////////////////////////////////////////////////////////////
function removeCcUser(userId,projectId) {
	//var cc = document.getElementById('cc_user').value;
	
	//document.getElementById('cclist').value += cc +",";


	var cclist = document.getElementById('cclist').value;
	if(cclist!=null)
	{
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
	//alert('add cc: '+woId+":"+cc);
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/add_default_cc.php",
		data: "projectId="+projectId+"&cc="+cclist+"&remove="+userId,
		success: function(msg) {
			document.getElementById('cc_list').innerHTML = msg;
		}
	});

	return false;
}

function endsWith(str, s){
var reg = new RegExp (s + "$");
return reg.test(str);
}
function updateUser(USER_ACCESS) {
	var userActiveStatus = 0;
	var userDeletedStatus = 0;
	var userAdminAccess = '';
	if($('#userActiveStatus').is(':checked')){
		userActiveStatus = 1;
	} 
	if($('#userDeletedStatus').is(':checked')){
		userDeletedStatus = 1;  
	}
	
	if($('#userAdminAccess').is(':checked')){
		userAdminAccess = 'admin';  
	}
	else
	{
		userAdminAccess = ''; 
	}
	var user_project_array = $("#user_project_array").val();
	
	var user_access_bit = "";
	var count_access = 0;
	for(var i = 0; i < USER_ACCESS.length; i++) {

		if($('#'+USER_ACCESS[i]).is(':checked')){
			user_access_bit = user_access_bit+'1';  
			count_access++;
		}
		else
		{
			user_access_bit = user_access_bit+'0';  
		}
	}
	if($('#user_company').val()=='2' && count_access<4 ||(count_access==4 && $('#ADMIN_ACCESS').is(':checked') == true))
	{
		var html = '<div class="file_message_confirm">';
			html +=	'<p> DPS User should have access to all section of Lighthouse, are you sure you want to revoke the permission? </p>';
			html +=	'<div style="clear: both;"></div>';
			html +=	'<div class="duplicate_buttons">';
			html +=	'	<button onClick="updateUserConfirmed('+userDeletedStatus+','+userActiveStatus+',\''+userAdminAccess+'\',\''+user_access_bit+'\');return false;"><span>Yes</span></button>';
			html +=	'	<button class="cancel" onClick="$(\'.file_message_confirm\').css({display:\'none\'}); return false;"><span>No</span></button>';
			html +=	'	<div style="clear: both;"></div>';
			html +=	'</div>';
			html +=	'</div>';
		$('body').append(html);
		$('.file_message_confirm').css({display:'block'});
	}
	else
	{
		updateUserConfirmed(userDeletedStatus,userActiveStatus,userAdminAccess,user_access_bit);
	}
}

function updateUserConfirmed(userDeletedStatus,userActiveStatus,userAdminAccess,user_access_bit)
{
	if($('#user_title_id').val()==$('#previousUserTitle').val()){
		isUserTitleChanged='N';
	}else{
		isUserTitleChanged='Y';
	}
	//alert("string"+$('#userProjectStr').val());
	var userStatus = $('#userStatus').val();
	if($('#userProjectArray').val()  == 0){
		isUserProjectChanged = 'N';
	}else{
		isUserProjectChanged = 'Y';
	}
	
	var user_company = $('#user_company').val();
	//alert("string2"+$('#userProjectArray').val()+"flag"+isUserProjectChanged);
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/updateUserInfo.php",
		data: "user_id="+$('#userID').val()+"&userTitle="+$('#user_title_id').val()+"&isUserTitleChanged="+isUserTitleChanged+"&userRole="+$('#user_Role_id').val()+"&userProgram="+$('#user_program option:selected').val()+"&userVendorName="+$('#user_vendor_name').val()+"&userDeletedStatus="+userDeletedStatus+"&userActiveStatus="+userActiveStatus+"&userAdminAccess="+userAdminAccess+"&user_access_bit="+user_access_bit+"&isUserProjectChanged="+isUserProjectChanged+"&userStatus="+userStatus+"&userProjectArray="+$('#userProjectArray').val()+"&user_company="+user_company,
		success: function(msg) {
			$('.message_required p').html('The User data has been updated Successfully.');
			$('.message_required').css({display:'block'});
        }
	});

	$('.file_message_confirm').css({display:'none'});
}
function updateProjectVersion(OP){
	var versionActiveStatus = 0;
	var versionDeletedStatus = 0;
	if($('#versionActiveStatus').is(':checked')){
		versionActiveStatus = 1;
	} 
	if($('#versionDeletedStatus').is(':checked')){
		versionDeletedStatus = 1;
	} 
	
	//changes for LH#18679
	if(($('#versionName').val()=='') || ($.trim($('#versionName').val()) == '') )
	  {
		  $('.message_required p').html('<br>Please enter the Version name.');
		  $('.message_required').css({display:'block'});
		  return false;
	  }
  //changes end
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/update_proj_ver.php",
		data: "OP="+OP+"&proj_id="+$('#admin_project_select').val()+"&versionID="+$('#versionID').val()+"&versionName="+$('#versionName').val()+"&versionDeletedStatus="+versionDeletedStatus+"&versionActiveStatus="+versionActiveStatus,
		success: function(msg) {
		if(msg == 'exist'){
			$('.message_required p').html('This Version has already associated with Project.');
			$('.message_required').css({display:'block'});
		}else{
			$('.message_required p').html('The Version has been updated Successfully.');
			$('.message_required').css({display:'block'});

			$('.message_required button').click(function(){
				var form = document.createElement("form");
				form.setAttribute("method", 'post');
				form.setAttribute("action", '/admin/index/projectversions/');

				 var hiddenField1 = document.createElement("input");
				 hiddenField1.setAttribute("type", "hidden");
				 hiddenField1.setAttribute("name", 'proj_id');
				 hiddenField1.setAttribute("value", $('#admin_project_select').val());
				 form.appendChild(hiddenField1);

				 document.body.appendChild(form);   
				 form.submit();
			});
        }
	}
	});
}
function updateProjectProduct(OP){
	var versionActiveStatus = 0;
	var versionDeletedStatus = 0;
	if($('#versionActiveStatus').is(':checked')){
		versionActiveStatus = 1;
	} 
	if($('#versionDeletedStatus').is(':checked')){
		versionDeletedStatus = 1;
	} 
	
	//changes for LH#18679
	if(($('#versionName').val()=='') || ($.trim($('#versionName').val()) == '') )
	{
	  $('.message_required p').html('<br>Please enter the Product name.');
	  $('.message_required').css({display:'block'});
	  return false;
	}
  //changes end
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/update_proj_product.php",
		data: "OP="+OP+"&proj_id="+$('#admin_project_select').val()+"&versionID="+$('#versionID').val()+"&versionName="+$('#versionName').val()+"&versionDeletedStatus="+versionDeletedStatus+"&versionActiveStatus="+versionActiveStatus,
		success: function(msg) {
		if(msg == 'exist'){
			$('.message_required p').html('This Product has already associated with Project.');
			$('.message_required').css({display:'block'});
			}else{
			$('.message_required p').html('The Product has been updated Successfully.');
			$('.message_required').css({display:'block'});

			$('.message_required button').click(function(){
				var form = document.createElement("form");
				form.setAttribute("method", 'post');
				form.setAttribute("action", '/admin/index/projectproduct/');

				 var hiddenField1 = document.createElement("input");
				 hiddenField1.setAttribute("type", "hidden");
				 hiddenField1.setAttribute("name", 'proj_id');
				 hiddenField1.setAttribute("value", $('#admin_project_select').val());
				 form.appendChild(hiddenField1);

				 document.body.appendChild(form);   
				 form.submit();
			});
        }
	}
	});
}
function updateProjectIteration(OP){
	var versionActiveStatus = 0;
	var versionDeletedStatus = 0;
	if($('#versionActiveStatus').is(':checked')){
		versionActiveStatus = 1;
	} 
	if($('#versionDeletedStatus').is(':checked')){
		versionDeletedStatus = 1;
	} 
	
	//changes for LH#18679
	if(($('#versionName').val()=='')||($.trim($('#versionName').val())==''))
	{
	  $('.message_required p').html('<br>Please enter the Iteration name.');
	  $('.message_required').css({display:'block'});
	  return false;
	}
  //changes end
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/update_proj_iteration.php",
		data: "OP="+OP+"&proj_id="+$('#admin_project_select').val()+"&versionID="+$('#versionID').val()+"&versionName="+$('#versionName').val()+"&versionDeletedStatus="+versionDeletedStatus+"&versionActiveStatus="+versionActiveStatus,
		success: function(msg) {
		if(msg == 'exist'){
			$('.message_required p').html('This Iteration has already associated with Project.');
			$('.message_required').css({display:'block'});
			}else{
			$('.message_required p').html('The Iteration has been updated Successfully.');
			$('.message_required').css({display:'block'});

			$('.message_required button').click(function(){
				var form = document.createElement("form");
				form.setAttribute("method", 'post');
				form.setAttribute("action", '/admin/index/projectiteration/');

				 var hiddenField1 = document.createElement("input");
				 hiddenField1.setAttribute("type", "hidden");
				 hiddenField1.setAttribute("name", 'proj_id');
				 hiddenField1.setAttribute("value", $('#admin_project_select').val());
				 form.appendChild(hiddenField1);

				 document.body.appendChild(form);   
				 form.submit();
			
			});
        }
		}	
	});
}
function addNewVersion(){
	$('#versionName').attr("disabled", false);
	$('#addButton').css({display:'none'});
	$('#versionName').val('');
	$('#versionID').val('');
	$('#updateBTN').css({display:'none'});
	$('#submitBTN').css({display:'block'});
}

function addNewProduct(){
	$('#versionName').attr("disabled", false);
	$('#addButton').css({display:'none'});
	$('#versionName').val('');
	$('#versionID').val('');
	$('#updateBTN').css({display:'none'});
	$('#submitBTN').css({display:'block'});
}

function addNewIteration(){
	$('#versionName').attr("disabled", false);
	$('#addButton').css({display:'none'});
	$('#versionName').val('');
	$('#versionID').val('');
	$('#updateBTN').css({display:'none'});
	$('#submitBTN').css({display:'block'});
}
function fetchUser(selectedUserID)
{
		userFirstName = $('#firstName').val();
		userLastName = $('#lastName').val();
		if(selectedUserID==null)
		{
			selectedUserID = '';
		}

		if(userFirstName==null)
		{
			userFirstName = '';
		}

		if(userLastName==null)
		{
			userLastName = '';
		}
		var form = document.createElement("form");
	    form.setAttribute("method", 'post');
	    form.setAttribute("action", '/admin/index/fetchUser/');

		 var hiddenField1 = document.createElement("input");
	     hiddenField1.setAttribute("type", "hidden");
	     hiddenField1.setAttribute("name", 'selectedUserID');
	     hiddenField1.setAttribute("value", selectedUserID);
	     form.appendChild(hiddenField1);

		 var hiddenField2 = document.createElement("input");
	     hiddenField2.setAttribute("type", "hidden");
	     hiddenField2.setAttribute("name", 'userFirstName');
	     hiddenField2.setAttribute("value", userFirstName);
	     form.appendChild(hiddenField2);

		 var hiddenField3 = document.createElement("input");
	     hiddenField3.setAttribute("type", "hidden");
	     hiddenField3.setAttribute("name", 'userLastName');
	     hiddenField3.setAttribute("value", userLastName);
	     form.appendChild(hiddenField3);

		 document.body.appendChild(form);   
	     form.submit();
}

function projectDefaultCC(proj_id)
{
	if(proj_id==null)
	{
		proj_id = '';
	}

	var form = document.createElement("form");
	form.setAttribute("method", 'post');
	form.setAttribute("action", '/admin/index/projectdefaultcc/');

	 var hiddenField1 = document.createElement("input");
	 hiddenField1.setAttribute("type", "hidden");
	 hiddenField1.setAttribute("name", 'proj_id');
	 hiddenField1.setAttribute("value", proj_id);
	 form.appendChild(hiddenField1);

	 document.body.appendChild(form);   
	 form.submit();
}
///////////18474///////
function QCprojectDefaultCC(proj_id)
{
	if(proj_id==null)
	{
		proj_id = '';
	}

	var form = document.createElement("form");
	form.setAttribute("method", 'post');
	form.setAttribute("action", '/admin/index/qcprojectdefaultcc/');

	 var hiddenField1 = document.createElement("input");
	 hiddenField1.setAttribute("type", "hidden");
	 hiddenField1.setAttribute("name", 'proj_id');
	 hiddenField1.setAttribute("value", proj_id);
	 form.appendChild(hiddenField1);

	 document.body.appendChild(form);   
	 form.submit();
}
////////////////
function workorderSLAReport()
{
	var form = document.createElement("form");
	form.setAttribute("method", 'post');
	form.setAttribute("action", '/admin/index/workordersla/');	

	 document.body.appendChild(form);   
	 form.submit();
}

function generateReport(){
    var admin_user_select = $('#admin_user_select').val();
    var admin_year_select = $('#admin_year_select').val();
	var admin_to_select = $('#admin_to_select').val();
    var admin_to_year_select = $('#admin_to_year_select').val();
	var admin_assign_select = $('#admin_assign_select').val();
	var admin_requested_select = $('#admin_requested_select').val();
	
	var from_date = new Date(admin_year_select,admin_user_select);
	var to_date = new Date(admin_to_year_select,admin_to_select);
	var diff= from_date- to_date;
	var weeks = Math.floor(Math.abs(diff) / (1000 * 7 * 24 * 60 * 60));
	
	if(admin_to_select!='' && admin_to_year_select!=''){
		if(parseInt(admin_user_select) > parseInt(admin_to_select) && parseInt(admin_year_select) > parseInt(admin_to_year_select) || parseInt(admin_user_select) < parseInt(admin_to_select) && parseInt(admin_year_select) > parseInt(admin_to_year_select) || parseInt(admin_user_select) > parseInt(admin_to_select) && parseInt(admin_year_select) == parseInt(admin_to_year_select)|| parseInt(admin_user_select) == parseInt(admin_to_select) && parseInt(admin_year_select) > parseInt(admin_to_year_select))
		{ 	

			alert('From Date should not be greater than to date'); return false;

		}
		

		if(weeks>17)
		{
			alert('Difference-in-Months exceeded the Max Limit of 5'); return false;
		}
		var d = 'month='+admin_user_select+'&year='+admin_year_select+'&to_month='+admin_to_select+'&to_year='+admin_to_year_select+'&assign_to='+admin_assign_select+'&admin_requested_select='+admin_requested_select;
	}else{
		var d = 'month='+admin_user_select+'&year='+admin_year_select+'&to_month='+admin_to_select+'&to_year='+admin_to_year_select+'&assign_to='+admin_assign_select+'&admin_requested_select='+admin_requested_select;
			//window.open('/_ajaxphp/admin_slareport.php?month='+admin_user_select+'&year='+admin_year_select);
	}
	
	
	jQuery.download('/_ajaxphp/admin_slareport.php',d);
}

function fetchProjVersion(proj_id,version_id)
{
		if(proj_id==null)
		{
			proj_id = '';
		}
		if(version_id==null)
		{
			version_id='';
		}
		var form = document.createElement("form");
	    form.setAttribute("method", 'post');
	    form.setAttribute("action", '/admin/index/projectversions/');

		 var hiddenField1 = document.createElement("input");
	     hiddenField1.setAttribute("type", "hidden");
	     hiddenField1.setAttribute("name", 'proj_id');
	     hiddenField1.setAttribute("value", proj_id);
	     form.appendChild(hiddenField1);

		 var hiddenField1 = document.createElement("input");
	     hiddenField1.setAttribute("type", "hidden");
	     hiddenField1.setAttribute("name", 'version_id');
	     hiddenField1.setAttribute("value", version_id);
	     form.appendChild(hiddenField1);		

		 document.body.appendChild(form);   
	     form.submit();
}
//28522
function fetchProjProduct(proj_id,version_id)
{
		if(proj_id==null)
		{
			proj_id = '';
		}
		if(version_id==null)
		{
			version_id='';
		}
		var form = document.createElement("form");
	    form.setAttribute("method", 'post');
	    form.setAttribute("action", '/admin/index/projectproduct/');

		 var hiddenField1 = document.createElement("input");
	     hiddenField1.setAttribute("type", "hidden");
	     hiddenField1.setAttribute("name", 'proj_id');
	     hiddenField1.setAttribute("value", proj_id);
	     form.appendChild(hiddenField1);

		 var hiddenField1 = document.createElement("input");
	     hiddenField1.setAttribute("type", "hidden");
	     hiddenField1.setAttribute("name", 'version_id');
	     hiddenField1.setAttribute("value", version_id);
	     form.appendChild(hiddenField1);		

		 document.body.appendChild(form);   
	     form.submit();
}
function fetchProjIteration(proj_id,version_id)
{
		if(proj_id==null)
		{
			proj_id = '';
		}
		if(version_id==null)
		{
			version_id='';
		}
		var form = document.createElement("form");
	    form.setAttribute("method", 'post');
	    form.setAttribute("action", '/admin/index/projectiteration/');

		 var hiddenField1 = document.createElement("input");
	     hiddenField1.setAttribute("type", "hidden");
	     hiddenField1.setAttribute("name", 'proj_id');
	     hiddenField1.setAttribute("value", proj_id);
	     form.appendChild(hiddenField1);

		 var hiddenField1 = document.createElement("input");
	     hiddenField1.setAttribute("type", "hidden");
	     hiddenField1.setAttribute("name", 'version_id');
	     hiddenField1.setAttribute("value", version_id);
	     form.appendChild(hiddenField1);		

		 document.body.appendChild(form);   
	     form.submit();
}
function customFieldName(field_id)
	{
	if(custom_name==null){
		custom_name = '';
	}

	var custom_name = $('#customFields').val();
	var form = document.createElement("form");
	form.setAttribute("method", 'post');
	form.setAttribute("action", '/admin/index/customfieldname/');	

	var hiddenField1 = document.createElement("input");
	hiddenField1.setAttribute("type", "hidden");
	hiddenField1.setAttribute("name", 'custom_name');
	hiddenField1.setAttribute("value", custom_name);
	form.appendChild(hiddenField1);

	if(typeof field_id != 'undefined'){
		var hiddenField2 = document.createElement("input");
		hiddenField2.setAttribute("type", "hidden");
		hiddenField2.setAttribute("name", 'field_id');
		hiddenField2.setAttribute("value", field_id);
		form.appendChild(hiddenField2);
	}	
	document.body.appendChild(form);   
	form.submit();
}

function addNewSite(){
	$('#addButton').css({display:'none'});
	$('#fieldnameInfo').css({display:'block'});
}

function updateFieldValue(OP){
	var fieldActiveStatus = 0;
	var fieldDeleteStatus = 0;
	var selectedCustomField = $("#customFields").val();
	if($('#fieldActiveStatus').is(':checked')){
		fieldActiveStatus = 1;
	} 
	if($('#fieldDeleteStatus').is(':checked')){
		fieldDeleteStatus = 1;
	} 

	if($('#fieldname').val().length == 0){
		$('.message_required p').html('Pls Fill the required feild.');
		$('.message_required').css({display:'block'});
		$('.message_required button').click(function(){
			$('.message_required').css({display:'none'});
		});
		return false;
	}

	$.ajax({
		type: "POST",
		url: "/_ajaxphp/update_site_name.php",
		data: "OP="+OP+ "&fieldid="+$('#fieldid').val()+ "&fieldname="+$('#fieldname').val()+ "&fieldDeleteStatus="+fieldDeleteStatus+ "&fieldActiveStatus="+fieldActiveStatus+"&custom_name="+selectedCustomField,
		success: function(msg) {
			if(msg == 'ADD'){
				$('.message_required p').html('The information has been added successfully.');
			}else{
				$('.message_required p').html('The change has been updated successfully.');
			}
			$('.message_required').css({display:'block'});

			$('.message_required button').click(function(){
				var form = document.createElement("form");
				form.setAttribute("method", 'post');
				form.setAttribute("action", '/admin/index/customfieldname/');

				 var hiddenField1 = document.createElement("input");
				 hiddenField1.setAttribute("type", "hidden");
				hiddenField1.setAttribute("name", 'custom_name');
				hiddenField1.setAttribute("value", selectedCustomField);
				 form.appendChild(hiddenField1);

				 document.body.appendChild(form);   
				 form.submit();
			});
    }
	});
}

function retrieveCustomFields(){
	var form = document.createElement("form");
	form.setAttribute("method", 'post');
	form.setAttribute("action", '/admin/index/retrieveCustomFields/');
	document.body.appendChild(form); 	
	form.submit();
}

function listCustomValues(){
	var selectedCustomField = $("#customFields").val();
	jQuery.getJSON("/_ajaxphp/get_custom_list.php?selectedDropDownfield="+selectedCustomField,
	function(json){     
		$('#customList').html(json);
	});
}

function editCustomField(listValue){
	listValue = typeof(listValue) != 'undefined' ? listValue : "";
	jQuery.getJSON("/_ajaxphp/add_edit_custom.php?selectedListValue="+listValue,
	function(json){     
		$('#customEditOrAdd').html(json);
	});
}
function qaGrid(field_id)
{

	var form = document.createElement("form");
	form.setAttribute("method", 'post');
	form.setAttribute("action", '/admin/index/qagrid/');	
	document.body.appendChild(form);   
	form.submit();
}

function rallyProjects(field_id)
{

	var form = document.createElement("form");
	form.setAttribute("method", 'post');
	form.setAttribute("action", '/admin/index/rallyprojectmap/');	
	document.body.appendChild(form);   
	form.submit();
}
function lhbasecamp(field_id)
{

	var form = document.createElement("form");
	form.setAttribute("method", 'post');
	form.setAttribute("action", '/admin/index/lhbasecampmapping/');	
	var hiddenField1 = document.createElement("input");
	hiddenField1.setAttribute("type", "hidden");
	hiddenField1.setAttribute("name", 'test_value');
	hiddenField1.setAttribute("value",'test_value');
	 form.appendChild(hiddenField1);
	document.body.appendChild(form);   
	form.submit();
}
function solrSearchLog(field_id)
{

	var form = document.createElement("form");
	form.setAttribute("method", 'post');
	form.setAttribute("action", '/admin/index/solrsearchlog/');	
	document.body.appendChild(form);   
	form.submit();
}

function setQadataGrid(){
	var qaProjectValues = new Array();
	$.each($("input[name='qaGrid[]']:checked"), function() {
		qaProjectValues.push($(this).val());
	  // or you can do something to the actual checked checkboxes by working directly with  'this'
	  // something like $(this).hide() (only something useful, probably) :P
	});
	if(qaProjectValues.length > 0){
		$.post("/admin/index/inserqadata/pid/"+qaProjectValues,
				function(data){
						$('.message_required p').html('The information has been updated successfully.');
						$('.message_required').css({display:'block'});
				});
	}else{
		$('.message_required p').html('Please select at least one Project.');
		$('.message_required').css({display:'block'});
	}
}


function mappLHRalyProjectes(){

	
	
	var lh_project = $('#lh_project').val();
	var rally_project = $('#rally_project').val();
	if(lh_project == ''){
		$('.message_required p').html('Please select LH Project .');
		$('.message_required').css({display:'block'});
		return false;
	
	
	}if(rally_project == ''){
		$('.message_required p').html('Please select Rally Project .');
		$('.message_required').css({display:'block'});
		return false;
	}
	
	if(lh_project != '' && lh_project != ''){
		$('#rallyReportbtn').attr("onclick","");
		$.post("/admin/index/maprojectlisting",{lh:lh_project,rally:rally_project},
				function(data){
						
						if($.trim(data) != ''){
							var mappCounter =$('#mappCounter').val();
							if(mappCounter != '' || mappCounter == 0){
								$('.adminTh').after(data);
								$('#mappCounter').val($('#mappCounter').val()+1);
							}else{
								$('.adminTr').before(data);
							}
							$('#rallyReportbtn').attr("onclick","return mappLHRalyProjectes();");
							$('#lh_project :selected').remove();
							$('#rally_project :selected').remove();
							$('.message_required p').html('The information has been updated successfully.');
							$('.message_required').css({display:'block'});
						}
				});
	
	}


}



function deleteRallyProject(id){
	$.post("/admin/index/deletemaprojectlisting",{id:id},
				function(data){
						if(data != ''){
							$('#tr_'+id).css('display','none');
							$('.message_required p').html('The information has been delete successfully.');
							$('.message_required').css({display:'block'});
						}
						
				});



}

function deleteBsProject(id){
	$.post("/admin/index/deletebsmaprojectlisting",{id:id},
			function(data){
					if(data != ''){
						$('#tr_'+id).css('display','none');
						$('.message_required button').removeAttr("onclick");
						$('.message_required button').attr("onclick","location.reload();");
						$('.message_required p').html('The information has been delete successfully.');
						$('.message_required').css({display:'block'});
					}
					
			});
}

function generateUsers(){
	
	window.location.href = "/admin/index/exportusers";
	
	
	
}
function createExportUsersLog(){
	window.open('/_ajaxphp/exportUsers.php');
	//window.location.href = "/admin/index/exportusers";


}

function basecampUserMapping(){
	var lh_users = $('#lh_users').val();
	var lh_basecamp = $('#bs_project').val();
	if(lh_basecamp == ''){
		$('.message_required p').html('Please select Basecamp Project .');
		$('.message_required').css({display:'block'});
		return false;
	
	
	}if(lh_users == ''){
		$('.message_required p').html('Please select LH User .');
		$('.message_required').css({display:'block'});
		return false;
	}
	
	if((lh_basecamp != '') && (lh_users != '')){
		
		$('#basecampReportbtn').attr("onclick","");
		$.post("/admin/index/basecampmaprojectlisting",{lh_basecamp:lh_basecamp,lh_users:lh_users},
				function(data){
						
						if($.trim(data) != ''){
							var mappCounter =$('#mappCounter').val();
							if(mappCounter != '' || mappCounter == 0){
								$('.adminTh').after(data);
								$('#mappCounter').val($('#mappCounter').val()+1);
							}else{
								$('.adminTr').before(data);
							}
							$('#basecampReportbtn').attr("onclick","return basecampUserMapping();");
							$('#lh_users :selected').remove();
							//$('#bs_project :selected').remove();
							$('.message_required p').html('The information has been updated successfully.');
							$('.message_required').css({display:'block'});
						}
				});
		
		
	}
	
	
}
