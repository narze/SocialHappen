$(function(){
	$('a.a-dashboard').live('click',function(){
		set_loading();
		$('div#main').load(base_url+'tab/dashboard/'+page_id+'/'+token,function(){
			trigger_countdown = function (){
				$('.campaign-end-time').each(function(){
					end_time = Date.createFromMysql($(this).text());
					$(this).replaceWith($("<p></p>").countdown({
						until: end_time,
						format: 'HMS',
						layout: '<strong>{hnn}h {sep} {mnn}m {sep} {snn}s</strong>'})
					.removeClass('hasCountdown'));
				});
			};
			
			if(is_guest){
				$.fancybox({
					href: base_url+'tab/guest'
				});
				$('a.a-close').live('click',function(){
					$.fancybox.close();
				});
				$('a.a-signup').live('click',function(){
					$.fancybox({
						href: base_url+'tab/signup'
					});
					$('form.signup-form').live('submit', function() {
						$(this).ajaxSubmit({target:'#signup-form'});
						return false;
					});
				});
			} else if(page_app_installed_id!=0) {
				$.fancybox({
					href: base_url+'tab/app_installed/'+ page_app_installed_id
				});
				$('a.a-close').live('click',function(){
					$.fancybox.close();
				});
			} else if(page_installed==0){
				$.fancybox({
					href: base_url+'tab/page_installed/'+ page_id
				});
				$('a.a-close').live('click',function(){
					$.fancybox.close();
				});
			}
			
			var mode = '';
			function get_apps_campaigns(page_index,jq){
				$('div.list_app-camp').load(base_url+'tab/apps_campaigns/'+page_id+'/'+per_page+'/'+(page_index * per_page) + mode,trigger_countdown);
			}
			
			$('div.list_app-camp').load(base_url+'tab/apps_campaigns/'+page_id+'/'+per_page,function(){
				$('a.a-app-campaign').click(function(){
					$.getJSON(base_url+"page/json_count_apps/"+page_id,function(app_count){
						$.getJSON(base_url+"page/json_count_campaigns/"+page_id,function(campaign_count){
							count = app_count+campaign_count;
							mode = '';
							$('.pagination-app-campaign').pagination(count, {
								items_per_page:per_page,
								callback:get_apps_campaigns,
								load_first_page:true,
								next_text:null,
								prev_text:null
							});
						});
					});
					$(this).parent('li').parent('ul').find('li a').removeClass('active');
					$(this).addClass('active');
				});
				
				$('a.a-app').click(function(){
					$.getJSON(base_url+"page/json_count_apps/"+page_id,function(app_count){
					
						count = app_count;
						mode = '?filter=app';
						$('.pagination-app-campaign').pagination(count, {
							items_per_page:per_page,
							callback:get_apps_campaigns,
							load_first_page:true,
							next_text:null,
							prev_text:null
						});
					
					});
					$(this).parent('li').parent('ul').find('li a').removeClass('active');
					$(this).addClass('active');
				});
				
				$('a.a-campaign').click(function(){
					
						$.getJSON(base_url+"page/json_count_campaigns/"+page_id,function(campaign_count){
							count = campaign_count;
							mode = '?filter=campaign';
							$('.pagination-app-campaign').pagination(count, {
								items_per_page:per_page,
								callback:get_apps_campaigns,
								load_first_page:true,
								next_text:null,
								prev_text:null
							});
						});
					
					$(this).parent('li').parent('ul').find('li a').removeClass('active');
					$(this).addClass('active');
				});
				$('a.a-app-campaign').click();
			});
			
			function filter_activities(page_index,jq){
				$('div.list_resent-activity').children('ul').children('li').hide();
				for(i=0;i<per_page;i++){
					$('div.list_resent-activity').children('ul').children('li:eq('+((page_index * per_page)+i)+')').show();
				}
			}
			
			function activitiy_pagination(){
				count = $(this).children('ul').children('li').length;
				$('.pagination-activity').pagination(count, {
					items_per_page:per_page,
					callback:filter_activities,
					load_first_page:true,
					next_text:null,
					prev_text:null
				});
			}
			
			$('div.list_resent-activity').load(base_url+'tab/activities/'+page_id,function(){
				$('a.a-activity-app-campaign').click(function(){
					$('div.list_resent-activity').load(base_url+'tab/activities/'+page_id,activitiy_pagination);
					$(this).parent('li').parent('ul').find('li a').removeClass('active');
					$(this).addClass('active');
				});
				
				$('a.a-activity-app').click(function(){
					$(this).parent('li').parent('ul').find('li a').removeClass('active');
					$(this).addClass('active');
					$('div.list_resent-activity').load(base_url+'tab/activities/'+page_id+'?filter=app',activitiy_pagination);
				});
				
				$('a.a-activity-campaign').click(function(){
					$(this).parent('li').parent('ul').find('li a').removeClass('active');
					$(this).addClass('active');
					$('div.list_resent-activity').load(base_url+'tab/activities/'+page_id+'?filter=campaign',activitiy_pagination);
				});
				
				$('a.a-activity-me').click(function(){
					$(this).parent('li').parent('ul').find('li a').removeClass('active');
					$(this).addClass('active');
					$('div.list_resent-activity').load(base_url+'tab/activities/'+page_id+'?filter=me',activitiy_pagination);
				});
				$('a.a-activity-app-campaign').click();
			});
		});
		return false;
	});
	$('a.a-dashboard').click();
});