$(function(){
	$('a.a-dashboard').live('click',function(){
		set_loading();
		$('div#main').load(base_url+'tab/dashboard/'+page_id+'/'+token,function(){
			$('.campaign-end-time').each(function(){
				end_time = Date.createFromMysql($(this).text());
				$(this).replaceWith($("<p></p>").countdown({
					until: end_time,
					format: 'HMS',
					layout: '<strong>{hnn}h {sep} {mnn}m {sep} {snn}s</strong>'})
				.removeClass('hasCountdown'));
			});
			
			if(is_guest){
				$.fancybox({
					content: 'you are guest'
				});
			} else if(page_installed) {
				$.fancybox({
					content: 'page installed'
				});
			} else if(app_installed){
				$.fancybox({
					content: 'app installed'
				});
			}
			
			$('div.list_resent-activity').load(base_url+'tab/activities/'+page_id,function(){
				$('a.a-activity-app-campaign').click(function(){
					$(this).parent('li').parent('ul').find('li a').removeClass('active');
					$(this).addClass('active');
					$('div.list_resent-activity').load(base_url+'tab/activities/'+page_id+' div.list_resent-activity');
				});
				
				$('a.a-activity-app').click(function(){
					$(this).parent('li').parent('ul').find('li a').removeClass('active');
					$(this).addClass('active');
					$('div.list_resent-activity').load(base_url+'tab/activities/'+page_id+'?filter=app div.list_resent-activity');
				});
				
				$('a.a-activity-campaign').click(function(){
					$(this).parent('li').parent('ul').find('li a').removeClass('active');
					$(this).addClass('active');
					$('div.list_resent-activity').load(base_url+'tab/activities/'+page_id+'?filter=campaign div.list_resent-activity');
				});
				
				$('a.a-activity-me').click(function(){
					$(this).parent('li').parent('ul').find('li a').removeClass('active');
					$(this).addClass('active');
					$('div.list_resent-activity').load(base_url+'tab/activities/'+page_id+'?filter=me div.list_resent-activity');
				});
			});
			
			$('a.a-app-campaign').click(function(){
				$(this).parent('li').parent('ul').find('li a').removeClass('active');
				$(this).addClass('active');
				$('div.list_app-camp').load(base_url+'tab/dashboard/'+page_id+' div.list_app-camp');
			});
			
			$('a.a-app').click(function(){
				$(this).parent('li').parent('ul').find('li a').removeClass('active');
				$(this).addClass('active');
				$('div.list_app-camp').load(base_url+'tab/dashboard/'+page_id+'?filter=app div.list_app-camp');
			});
			
			$('a.a-campaign').click(function(){
				$(this).parent('li').parent('ul').find('li a').removeClass('active');
				$(this).addClass('active');
				$('div.list_app-camp').load(base_url+'tab/dashboard/'+page_id+'?filter=campaign div.list_app-camp');
			});
		});
		return false;
	});
	$('a.a-dashboard').click();
});