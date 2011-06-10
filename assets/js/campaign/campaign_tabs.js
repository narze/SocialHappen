$(function(){	
	function show_tab(name){
		$('.wrapper-details').hide();
		$('.tab-content ul li').removeClass('active');
		$('.tab-content ul li.'+name).addClass('active');
		
		var style_name = '';
		if(name == 'stat') style_name = 'main';
		else if(name == 'users') style_name = 'member';
		$('link.page').attr('disabled',true);
		$('link.page#'+style_name).removeAttr('disabled');
		$('.wrapper-details.'+name).show();
	}

	$('.tab-content ul li.stat a').click(function(){
		show_tab('stat');
	});
	$('.tab-content ul li.users a').click(function(){
		show_tab('users');
	});
	
	$('.tab-content ul li.stat a').click();
});