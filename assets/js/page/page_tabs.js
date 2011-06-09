$(function(){	
	function show_tab(name){
		$('.wrapper-details').hide();
		$('.tab-content ul li').removeClass('active');
		$('.tab-content ul li.'+name).addClass('active');
		
		var style_name = '';
		if(name == 'apps') style_name = 'main';
		else if(name == 'campaigns') style_name = 'campaign';
		else if(name == 'users') style_name = 'member';
		else if(name == 'report') style_name = 'main'; //no report css yet
		$('link.page').attr('disabled',true);
		$('link.page#'+style_name).removeAttr('disabled');
		$('.wrapper-details.'+name).show();
	}
	
	$('.tab-content ul li.apps a').click(function(){
		show_tab('apps');
	});
	$('.tab-content ul li.campaigns a').click(function(){
		show_tab('campaigns');
	});
	$('.tab-content ul li.users a').click(function(){
		show_tab('users');
	});
	$('.tab-content ul li.report a').click(function(){
		show_tab('report');
	});
	
	$('.tab-content ul li.apps a').click();
});