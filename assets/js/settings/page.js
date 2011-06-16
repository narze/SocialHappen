$(function(){
	$('form.page').live('submit',function(){
		formData = $(this).serializeArray();					
		$.post(base_url+"settings/page/"+param_id,formData,function(returnData){
			$('.form').replaceWith(returnData);
		});
		return false;
	});
});