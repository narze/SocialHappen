$(function(){
	$('div.popup_payment a.bt-continue').live('click', function() {
		$('.payment-form').ajaxSubmit({target:'div.form'});
		return false;
	});
	
	$('div.popup_payment-confirm a.bt-continue').live('click', function() {
		
	});
	
	$('div.popup_payment-complete a.bt-close').live('click', function() {
		
	});
});