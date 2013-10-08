function checkDirName(fileForm){

	
	$('#file_upload_dimmer').css({display:'block'});
	
	$('#file_upload_dimmer').css({backgroundColor:'#FFFFFF'});
	$('#file_upload_dimmer').css({opacity:'0.7'});
	$('#file_upload_dimmer').css({filter:'0.7'});
	
	ajaxUploadFile(fileForm);
	//fileForm.upload_file.value="";
	



}
$(document).ready(function() {
changeImage();

$('#profile_cancel').click(function(){
	window.location.href= "/workorders";
	
	

});

$('#profile_save').click(function(){
	
	var phone = $('#phone').val();
	var user_company = $('#user_company').val();
	if(user_company == '-1'){
		$('.message_required p').html('Please select a company.');
		$('.message_required').css({display:'block'});
	}
	/*if(user_company == 'other'){
		
		$('.message_required p').html('Please select a company.');
		$('.message_required').css({display:'block'});
	}*/
	
	$.ajax({
		type: "POST",
		url: "/workorders/profile/companyupdate",
		data: "companyId="+user_company+"&phone="+phone,
		success: function(msg) {
			$('.message_required p').html('User Profile has completed.You Can start to create Workorders');
			$('.message_required').css({display:'block'});
		}
	});



});

//Code Ends
});
function changeImage()
{

	var userID = $('#userId').val();
	
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/getUserImage.php",
		data: "userID="+userID,
		success: function(msg) {
			$('#requestor_photo').attr('src',msg);
		}
	});
}
function ajaxUploadFile(fileForm){
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
			url:'/workorders/profile/imageupdate',
			secureuri:false,
			fileElementId:'image_upload',
			dataType: 'text',
			success: function (data, status)
			{
				if(data.search("success")>='0'){
					
					$('.message_required p').html('File uploaded successfully.');
					$('.message_required').css({display:'block'});
				}
				else if(data.search("You")>='0'){
					
					$('.message_required p').html('File extention not supported.');
					$('.message_required').css({display:'block'});
				}
				else if(data.search("Exdeed")>='0'){
					
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
				changeImage()
				$('#image_upload').val('');
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