/**** JS file for Amr khaled orders webpage by Khalifa computer group 11-1-2018***/
//////////////////////////////////////Application initialization ///////////
$(document).ready(function () {
	
	$(document).on('change', '#city',function(event){
		populate_areas()
	});
});

/****specific functions****/

	function submitorder(){
		$.confirm({
					title: 'تأكيد',
					content: 'هل أنت متأكد من رغبك في ارسال الطلب؟',
					buttons: {
						confirm: {
									text: 'تأكيد',
									btnClass: 'btn-blue',
									action: function () {
										
									}
								},
						cancel: {
									text: 'لا',
									action: function () {
									}
								}
					}
				});
	}
	function populate_areas(){
		var selected_city=$('#city :selected').val()
		var areas_arr = $("#city option[value="+selected_city+"]").attr("data-areas").split(",");
		var area_option='<option value=""></option>';
		for(ii=0;ii<areas_arr.length;ii++ ){
			area_option=area_option+'<option value="'+areas_arr[ii]+'">'+areas_arr[ii]+'</option>';
		}
		$('#area').children().remove()
		$('#area').append(area_option);
	}

   
    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
