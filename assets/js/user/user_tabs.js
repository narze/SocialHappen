$(function(){	
	function show_tab(name){
		$('[class*="wrapper-details"]').hide();
		$('.tab-content ul li').removeClass('active');
		$('.tab-content ul li.'+name).addClass('active');
		$('[class*="wrapper-details"][class*="'+name+'"]').show();
	}

	$('.tab-content ul li.stat a').click(function(){
		show_tab('stat');
		render_user_stat(page_id, user_id);
	});
	$('.tab-content ul li.activities a').click(function(){
		show_tab('activities');
	});
	
	$('.tab-content ul li.stat a').click();
});