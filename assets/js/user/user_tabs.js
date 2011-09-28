$(function(){	
	
	$('.wrapper-details div.tab').hide();
	$('.wrapper-details div.tab').eq(0).show();
	
	$('.tab-content ul li').click(function (){
		$(this).siblings().removeClass('active');
		$(this).addClass('active');
		$('.wrapper-details div.tab').hide();
		$('.wrapper-details div.tab').eq( $(this).index() ).show();
	});

	$('.tab-content ul li.stat a').click(function(){
		render_user_stat(page_id, user_id);
	});

	render_user_stat(page_id, user_id);
});