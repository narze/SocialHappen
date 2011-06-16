$(function(){
	$('form').live('submit',function(){
		formData = $(this).serializeArray();					
		$.post(base_url+"settings/account/"+param_id,formData,function(returnData){
			$('.form').replaceWith(returnData);
		});
		return false;
	});
});