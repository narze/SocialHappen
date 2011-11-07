$(function(){

	function load_get_started() {
		set_loading();
		$('div#main').load(base_url+'tab/get_started/'+page_id,function(){
			$('div.icon-help').die('hover').live('hover', function() {
				$(this).siblings('.tips').toggle();
			});
		});
	}
	
	function load_dashboard(){
		var viewas = '';
		if($(this).hasClass('view-as-user')){
			viewas = 'viewas=user';
		} else if ($(this).hasClass('view-as-guest')){
			viewas = 'viewas=guest';
		}
		
		set_loading();
		$('div#main').load(base_url+'tab/dashboard/'+page_id,function(){

			trigger_countdown = function (){
				$('.campaign-end-time').each(function(){
					end_time = Date.createFromMysql($(this).text());
					$(this).replaceWith($("<p></p>").countdown({
						until: end_time,
						format: 'DHMS',
						layout: '<strong>{dn}days {hnn}h {sep} {mnn}m {sep} {snn}s</strong>'})
					.removeClass('hasCountdown'));
				});
			};
			
			var mode = '?';
			function get_apps_campaigns(page_index,jq){
				$('div.list_app-camp').load(base_url+'tab/apps_campaigns/'+page_id+'/'+per_page+'/'+(page_index * per_page) + mode + viewas,trigger_countdown);
				if($('div.pagination-app-campaign').find('a').length == 0) {
					$('div.pagination-app-campaign').find('div.pagination').remove();
				}
			}
			
			$('div.list_app-camp').load(base_url+'tab/apps_campaigns/'+page_id+'/'+per_page,function(){
				$('a.a-app-campaign').click(function(){
					$.getJSON(base_url+"page/json_count_apps/"+page_id,function(app_count){
						$.getJSON(base_url+"page/json_count_campaigns/"+page_id,function(campaign_count){
							count = app_count+campaign_count;
							mode = '?';
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
						mode = '?filter=app&';
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
							mode = '?filter=campaign&';
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
				
				if($('div.pagination-activity').find('a').attr('href') == '#') { 
					$('div.pagination-activity').find('a').removeAttr('href'); // Remove href="#"
				}
				
				if($('div.pagination-activity').find('a').length == 0) {
					$('div.pagination-activity').find('div.pagination').remove();
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
				
				$.each( $('span.timeago'), function(index, span) { 
				  $('span.timeago').eq(index).text($.timeago(new Date(parseInt( $(span).text() , 10) * 1000)));
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
	}
	
	function load_notification(){
		set_loading();
		$('div#main').load(base_url+'tab/notifications/'+user_id+'?return_url='+return_url, function(){
			$('a.back-to-app').attr('href', return_url);
			
			$.getJSON(base_url+"tab/json_count_user_notifications/"+user_id, function(count){
				if(count <= notifications_per_page) {
					get_user_notifications(0);
				} else {
					$('div.paging').pagination(count, {
						items_per_page:notifications_per_page,
						callback:get_user_notifications,
						load_first_page:true,
						next_text: '>',
						prev_text: '<'
					});
				}
			});
		});
		
		$('.toggle').find('ul').hide();
		return false;
	}
	
	function get_user_notifications(page_index) {
		set_loading();
		$.getJSON(base_url+'tab/json_get_notifications/'+user_id+'/'+notifications_per_page+'/'+(page_index * notifications_per_page), function(json){
			var template = $('div.notifications-list ul li:first-child').removeClass('unread');
			if(json.length == 0) {
				template.remove();
			} else {
				var notification_id_list = [];
				var old_li = $('div.notifications-list ul li'); //Prevent page auto scroll
				for(i in json) {
					var li = template.clone();
					if(json[i].read == false) { 
						li.addClass('unread') 
						notification_id_list.push( json[i]._id.$id );
					}
					li.find('img').attr('src', json[i].image );
					li.find('p.title').text( 'SocialHappen' );
					li.find('div.desc p').html( json[i].message );
					li.find('div.date').text( $.timeago(new Date(parseInt(json[i].timestamp, 10) * 1000)) );
					$('div.notifications-list ul').append(li);
				}
				if(notification_id_list.length > 0) {
					$.get(base_url + '/api/read_notification?user_id='+user_id+'&notification_list='+JSON.stringify(notification_id_list), function(result){
						//mark as read
					}, 'json');
				}
				old_li.remove(); //Prevent page auto scroll
			}
		});

		if($('div.pagination').find('a').attr('href') == '#') {
			$('div.pagination').find('a').removeAttr('href'); // Remove href="#"
		}
	}
	
	$('a.a-dashboard, a.bt-dashboard').live('click',function(){
		load_dashboard();
		return false;
	});
	
	$('a.bt-get-started').live('click',function(){
		load_get_started();
		return false;
	});
	
	$('a.a-notification').live('click',function(){
		load_notification();
		return false;
	});
	
	$('ul.counter li').live('mouseover mouseout', function(event) {
		if (event.type == 'mouseover') {
			var tooltips = $('<div class="tooltips"><span></span></div>');
			if( $(this).attr('alt').length > 0 )
			{
				tooltips.append( $(this).attr('alt') );
				$(this).append(tooltips);
			}
		} else {
			$(this).find('div.tooltips').remove();
		}
	});
	
	switch(view) {
		case 'notification' : load_notification(); break;
		default : load_dashboard();
	}
	
});