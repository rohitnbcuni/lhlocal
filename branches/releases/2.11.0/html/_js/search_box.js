function toggleDiv(){      
 $('#popup_top').toggle();
    }
	

function hide_searchpopup(){
      $('#popup_top').hide();
    }

	
$(document).ready(function(){	
	$('div.bt_advSearch').mouseover(function(){
	$('#popup_top').toggle();
	
	
	}).mouseout(function(){
	$('#popup_top').toggle();
	
	});
	
	
	
	$("#bt_search").click(function(){
	
		if($.trim($('#search_text').val()) == ''){
			$('#search_text').focus();
			$('#search_text').css('border-color', 'red');
		
		}else{
			$('#search_box_form').submit();
		}
	
	});
});	

