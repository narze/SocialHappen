$(function(){
	
	$('div.popup_payment a.bt-continue').live('click', function() {
		var loading = $('<img src="'+base_url+'assets/images/loading.gif" />');
		$('a.bt-continue').after(loading).hide();
		$('.payment-form').ajaxSubmit({
			async:true,
			success: function(data){
				alert("You will now be directed to Paypal's website to continue your order");
				window.location = data;
			}
		});
		return false;
	});
	
	$('div.popup_payment-confirm a.bt-continue').live('click', function() {

		var loading = $('<img src="'+base_url+'assets/images/loading.gif" />');
		$('a.bt-continue').after(loading).hide();
		$('.payment-form').ajaxSubmit({
			async:true,
			success: function(json){
				//console.log(json);
				json = JSON.parse(json);
				if(json.status == 'OK')
				{
					$.fancybox({
						href: base_url+'payment/payment_complete',
						transitionIn: 'elastic',
						transitionOut: 'elastic',
						padding: 0,
						scrolling: 'no',
						hideOnOverlayClick: false,
						showCloseButton: false
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
	
	
	$('a.bt-select-package').live('click',function(){
		var package_id = $(this).attr('rel');
		var pop;

		if(is_login()){
			pop = 'payment/payment_form?package_id='+ package_id;
		} else {
			pop = 'home/facebook_connect?package_id='+ package_id;
		}
		
		$.fancybox({
			href: base_url+pop,
			transitionIn: 'elastic',
			transitionOut: 'elastic',
			padding: 0,
			scrolling: 'no'
		});
		
	});
	
	
	if(get_query(window.location.href, 'payment') == 'true')
	{ 
		package_id = get_query(window.location.href, 'package_id');

		$.fancybox({
			href: base_url+'payment/payment_form?package_id='+ package_id,
			transitionIn: 'elastic',
			transitionOut: 'elastic',
			padding: 0,
			scrolling: 'no'
		});
	}
});