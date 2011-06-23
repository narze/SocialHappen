$(function(){
	$('form.account').live('submit',function(){
		formData = $(this).serializeArray();					
		$.post(base_url+"settings/account/"+user_id,formData,function(returnData){
			$('.form').replaceWith(returnData);
		});
		return false;
	});
});