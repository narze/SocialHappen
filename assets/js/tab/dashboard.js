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

			$('.main-memu .tab').click(function(){
				$(this).addClass('active').siblings().removeClass('active');
			});

			trigger_countdown = function (){
				$('.end-time-countdown').each(function(){
					end_time = Date.createFromMysql($(this).text());
					$(this).countdown({
						until: end_time,
						format: 'DHMS',
						layout: '{dn}days {hnn}h {sep} {mnn}m {sep} {snn}s'});
				});
			};

			campaign = function () {
				$('div.list_app-camp').load(base_url+'tab/campaigns/'+page_id+'/'+per_page,function(){
					$('.tab-head.campaign a').click(function(){
						mode = '?filter='+$(this).attr('data-filter')+'&';
						campaign_pagination();
						$(this).addClass('active').siblings().removeClass('active');
						trigger_countdown();
					});
					$('.tab-head.campaign a.active').click();
				});
			}
			
			var mode = '?';
			filter_campaigns = function (page_index,jq){
				$('div.list_app-camp').load(base_url+'tab/campaigns/'+page_id+'/'+per_page+'/'+(page_index * per_page) + mode + viewas,trigger_countdown);
				/*
				$.get(base_url+'tab/campaigns/'+page_id+'/'+per_page+'/'+(page_index * per_page) + mode + viewas, function(result) {
					trigger_countdown();
					console.log(result);
					$('div.list_app-camp').html(result);
				});
				*/
				if($('div.pagination-app-campaign').find('a').length == 0) {
					$('div.pagination-app-campaign').find('div.pagination').remove();
				}
			}

			campaign_pagination = function (){
				$.getJSON(base_url+"page/json_count_campaigns/"+page_id,function(campaign_count){
					$('.pagination-app-campaign').pagination(campaign_count, {
						items_per_page:per_page,
						callback:filter_campaigns,
						load_first_page:true,
						next_text:null,
						prev_text:null
					});
				});
			}

			activity = function () {
				$('div.list_resent-activity').load(base_url+'tab/activities/'+page_id,function(){
					$('.tab-head.activity .tab').click(function(){
						var filter = '?filter='+ $(this).attr('data-filter');
						$('div.list_resent-activity').load(base_url+'tab/activities/'+page_id+filter, activitiy_pagination);
						$(this).addClass('active').siblings().removeClass('active');
					});
					$('.tab-head.activity .tab').eq(0).click();
				});
			}
			
			filter_activities = function (page_index,jq){
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
			
			activitiy_pagination = function (){
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
			
			

			reward = function () {
				$('div.list_reward').load(base_url+'tab/reward/'+page_id,function(){
					$('.tab-head.reward .tab').click(function(){
						var filter = '?filter='+ $(this).attr('data-filter');
						$('div.list_reward').load(base_url+'tab/reward/'+page_id+'/'+filter, reward_pagination);
						$(this).addClass('active').siblings().removeClass('active');
					});
					$('.tab-head.reward .tab').eq(0).click();
				});
			}

			filter_reward = function (page_index,jq){
				trigger_countdown();
				$('div.list_reward').find('.reward-item').hide().eq(page_index).show();
				
				if($('div.pagination-reward').find('a').attr('href') == '#') { 
					$('div.pagination-reward').find('a').removeAttr('href'); // Remove href="#"
				}
				
				if($('div.pagination-reward').find('a').length == 0) {
					$('div.pagination-reward').find('div.pagination').remove();
				}
			}

			reward_pagination = function (){
				count = $(this).find('.reward-item').length;
				$('.pagination-reward').pagination(count, {
					items_per_page:1,
					callback:filter_reward,
					load_first_page:true,
					next_text:null,
					prev_text:null
				});
			}

			campaign();
			reward();
			activity();
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