$(function(){	
	function show_tab(name){
		$('[class*="wrapper-details"]').hide();
		$('.tab-content ul li').removeClass('active');
		$('.tab-content ul li.'+name).addClass('active');
		$('[class*="wrapper-details"][class*="'+name+'"]').show();
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