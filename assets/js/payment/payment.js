$(function(){
	
	//Select payment method popup
	$('select[name=package_id]').live('change', function() {
		var selected = $("select[name=package_id] option:selected");
		var package_id = selected.val();
		var index = selected.index();
		//alert(index);
		$('#package-image img').hide();
		$('#package-image img:eq('+index+')').show();
		$('#package-detail p').hide();
		$('#package-detail p:eq('+index+')').show();
		if( package_id == $('input[name=free_package_id]').val() ) //Hide payment when select free package
		{
			$('#select-payment').hide();
		}
		else
		{
			$('#select-payment').show();
		}
	});
	
	//Click continue in "Select payment method"
	$('div.popup_payment a.bt-continue').live('click', function() {
		var loading = $('<img src="'+base_url+'assets/images/loading.gif" />');
		$('a.bt-continue').after(loading).remove();
		$('.payment-form').ajaxSubmit({
			async:true,
			success: function(data){
				console.log(data);
				if( get_query(data.msg, 'popup') == 'thanks' ) { } //Free package, alert nothing
				else { alert("You will now be directed to Paypal's website to continue your order"); } //Paypal
				window.location = data.msg;
			},
			dataType: 'json'
		});
		return false;
	});
	
	//Payment summary page => show payment complete popup
	if( $('input[name="popup"]').val() == 'payment_complete' ) {
		$.fancybox({
			href: base_url+'payment/payment_complete/' + $('input[name="package_id"]').val(),
			transitionIn: 'elastic',
			transitionOut: 'elastic',
			padding: 0,
			scrolling: 'no',
		});
		
		$('div.popup_payment-complete a.bt-continue').live('click', function () {
			window.location = base_url + '?logged_in=true'; //Redirect to dashboard
		});
	}
	
	$('div.popup_payment-complete a.close').live('click', function() {
		$.fancybox.close();
		window.location = base_url;
	});
	
	
	$('a.bt-select-package').live('click',function(){
		var package_id = get_query( $(this).attr('href'), 'package_id' );
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
		
		return false;
		
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