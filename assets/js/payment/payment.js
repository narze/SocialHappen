$(function(){
	
	$('div.popup_payment a.bt-continue').live('click', function() {
		var loading = $('<img src="'+base_url+'assets/images/loading.gif" />');
		$('a.bt-continue').after(loading).hide();
		$('.payment-form').ajaxSubmit({
			async:true,
			success: function(data){
				window.location = data;
			}
		});
		return false;
	});
	
	$('div.popup_payment-confirm a.bt-continue').live('click', function() {

		$('.payment-form').ajaxSubmit({
			async:true,
			success: function(json){
				json = JSON.parse(json);
				console.log(json);
				if(json.status == 'OK')
				{
					$.fancybox({
						href: base_url+'payment/payment_complete',
						transitionIn: 'elastic',
						transitionOut: 'elastic',
						padding: 0,
						scrolling: 'no'
					});
				} 
				else
				{
					alert(json.message);
				}
			}
		});
		return false;
	});
	
	$('div.popup_payment-complete a.close').live('click', function() {
		$.fancybox.close();
		window.location = base_url;
	});
});