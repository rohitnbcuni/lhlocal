function toggleDiv(){      
 $('#popup_top').toggle();
    }
	

function hide_searchpopup(){
      $('#popup_top').hide();
    }

$(document).ready(function(){	
	$("#bt_search").click(function(){
	
		if($('#search_text').val() == ''){
			$('#search_text').focus();
		
		}else{
			$('#search_box_form').submit();
		}
	
	});
});	

