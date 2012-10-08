function toggleDiv(){      
 $('#popup_top').toggle();
    }
	

function hide_searchpopup(){
      $('#popup_top').hide();
    }

/*function submit_form() {
var frm = $('#search_box_form'); 
var search_text = $("input#search_text").val();
if ($("#search_par").is(':checked')){ 
var option=$("#search_par").val();}else{var option='';};
if ($("#search_par1").is(':checked')){
var option1=$("#search_par1").val();}else{var option1 ='';};
if ($("#search_par2").is(':checked')){
var option2=$("#search_par2").val();}else{var option2='';};
var dataString = 'search_text='+ search_text + '&search_par=' +option + '&search_par=' +option1+ '&search_par=' +option2;  
 $.ajax({
    type: "POST",
    url: "/search",
    data: dataString,
    success: function() {
       $('#content_container').html(msg) }
     });
}*/
