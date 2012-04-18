$(document).ready(function() {
		$("#login_form").validate({
			rules: {
				lh_username: {
				required: true,
				minlength: 2,
				maxlength:80
				},
				lh_password: {
					required: true,
					minlength: 2,
					maxlength:100
					}
	
			},
			messages: {
				lh_username: {
					required: " ",
					minlength: jQuery.format("Enter at least {0} characters")
					
				},
				lh_password: {
					required: " ",
					minlength: jQuery.format("Enter at least {0} characters")
				}
			}
		});
	$("#login_form_captcha").validate({
		
		rules: {
			lh_username: {
				required: true,
				minlength: 2,
				maxlength:80
				},
			lh_password: {
				required: true,
				minlength: 2,
				maxlength:100
				},
			recaptcha_response_field:{
					required: true	
				}	
			},
			messages: {
				lh_username: {
					required: " ",
					minlength: jQuery.format("Enter at least {0} characters")
				},
				lh_password: {
					required: " ",
					minlength: jQuery.format("Enter at least {0} characters")
				},
				recaptcha_response_field:{
					required:" "
				}
			}
	});
	

});