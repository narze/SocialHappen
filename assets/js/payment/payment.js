$(function(){
	
	$('select[name="package_id"]').live('change', function(){
		var id = $(this).find('option:selected').val();
		$('#package_detail span').hide();
		$('#package_detail span.package'+id).show();
		
		if(id==1) $('#select-payment').hide(); //This number is specific ID for free package, Need to be fixed!
		else $('#select-payment').show();
	});
	
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