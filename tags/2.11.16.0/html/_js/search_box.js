function toggleDiv(){      
 $('#popup_top').toggle();
    }
	

function hide_searchpopup(){
      $('#popup_top').hide();
    }

	
$(document).ready(function(){	

  
	/*$('#search_par').change(function () {
   
    if($(this).attr("checked")) {
        
        $("input:checkbox[@name=search_par[]]").attr('checked','checked');
    }
    else {
        $("input:checkbox[@name=search_par[]]").removeAttr('checked');
		}
	});*/

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


function didyoumean(){

	var dym = $('#dym').val();
	
	$('#search_text').val(dym);
	$('#search_par').val('All');
	$('#search_box_form').submit();

}

$(document).ready(function(){
	$('#bt_advSearch').click(function(){
		$('.advance_search_container').slideDown("show");
		
	});
});

	$(document).ready(function(){
	 /* $("#search-startdate").datepicker({
		numberOfMonths: 1,
		onSelect: function(selected) {
			$("#search-enddate").datepicker("option",{minDate}, selected)
		}
		});
		$("#search-enddate").datepicker({numberOfMonths: 1,	onSelect: function(selected) {
			$("#search-startdate").datepicker("option",{maxDate}, selected)
			}
		}); */
		$('.close_advance_search').click(function(){
			//$('.advance_search_container').css({display:'none'});
			$('.advance_search_container').slideUp("show");
		});
		var checkInDate = $('#search_startdate');
		var checkOutDate = $('#search_enddate');

		checkInDate.datepicker({ onClose: clearEndDate ,maxDate: 0});
		checkOutDate.datepicker({ beforeShow: setMinDateForEndDate,maxDate: 0 });

		function setMinDateForEndDate() {
			var d = checkInDate.datepicker('getDate');
			if (d) return { minDate: d }
		}
		function clearEndDate(dateText, inst) {
			checkOutDate.val('');
		}
		
	});
	 


	
function advance_search(){
	if(($.trim($('#all').val()) == '') && ($.trim($('#atLeastOne').val()) == '') && ($.trim($('#without').val()) == '')){
		$('#error_msg').html("At least one input box should have some value");
		return false;
	}else if(($.trim($('#all').val()) == '') && ($.trim($('#atLeastOne').val()) == '') && ($.trim($('#without').val()) != '')){
		$('#error_msg').html("Combination with NOT must have other operator(All/OR)");
		return false;
	}else if(($('#search_startdate').val() != '') && ($('#search_enddate').val() == '')){
		
		$('#error_msg').html("End Date must have some value");
		return false;
		
	}else if(($('#search_startdate').val() == '') && ($('#search_enddate').val() != '')){
		
		$('#error_msg').html("Start Date must have some value");
		return false;
		
	}else if($('input[name="search_fields[]"]:checked').length == 0) {
		   $('#error_msg').html('Please select at least one checkbox');
		   return false;
	}
		$('#advance_search_form').submit();
	}



