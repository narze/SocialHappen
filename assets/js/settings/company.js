$(function(){
	$('form.company').live('submit',function(){
		formData = $(this).serializeArray();					
		$.post(base_url+"settings/company/"+param_id,formData,function(returnData){
			$('.form').replaceWith(returnData);
		});
		return false;
	});
});