$(function(){
	$('div.popup_payment a.bt-continue').live('click', function() {
		var payment_method = $('input[name="payment_method"]:checked').val();
		$('.payment-form').attr('action',base_url+'payment/'+payment_method);
		$('.payment-form').ajaxSubmit({target:'div.form'});
		return false;
	});
	
	$('div.popup_payment-confirm a.bt-continue').live('click', function() {
		
	});
	
	$('div.popup_payment-complete a.bt-close').live('click', function() {
		
	});
});