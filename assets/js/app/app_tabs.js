$(function(){	
	function show_tab(name){
		$('[class*="wrapper-details"]').hide();
		$('.tab-content ul li').removeClass('active');
		$('.tab-content ul li.'+name).addClass('active');
		
		var style_name = '';
		if(name == 'campaigns') style_name = 'campaign';
		else if(name == 'users') style_name = 'member';
		$('link.app').attr('disabled',true);
		$('link.app#'+style_name).removeAttr('disabled');
		$('[class*="wrapper-details"][class*="'+name+'"]').show();
	}

	$('.tab-content ul li.campaigns a').click(function(){
		show_tab('campaigns');
	});
	$('.tab-content ul li.users a').click(function(){
		show_tab('users');
	});
	
	$('.tab-content ul li.campaigns a').click();
});