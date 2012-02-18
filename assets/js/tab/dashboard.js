$(function(){

	load_get_started = function () {
		set_loading();
		$('div#main').load(base_url+'tab/get_started/'+page_id,function(){
			$('div.icon-help').die('hover').live('hover', function() {
				$(this).siblings('.tips').toggle();
			});
		});
	}

	main_menu = function () {
		$('.main-menu .tab').click(function(){
			$(this).addClass('active').siblings().removeClass('active');
			switch($(this).attr('data-href')) 
			{
				case 'user-activities' : load_my_activities(); break;
				case 'user-badges' : load_my_badges(); break;
				case 'user-dashboard' : load_my_dashboard(); break;
				case 'page-reward' : load_page_reward(); break;
				case 'page-activities' : load_page_activities(); break;
				case 'page-dashboard' :
				default : load_page_dashboard();
			}
		});
	}
	
	load_page_dashboard = function (){
		set_loading();
		$('div#main').load(base_url+'tab/dashboard/'+page_id,function(){
			main_menu();
			campaign_box();
			reward_box();
			activity_box();
		});
	}

	load_page_reward = function () {
		
		var query = '?';

		sort_reward = function () {
			$(this).addClass('active').siblings('.tab').removeClass('active');
			var sort = $(this).data('sort');
			if(!sort){ return false; }
			else {
				switch(sort){
					case "value" : query = '?sort=value&order=desc';
					break;
					case "status" : query = '?sort=status';
					break;
					case "point" : query = '?sort=redeem.point&order=desc';
					break;
					case "date" : 
					default : query = '?sort=start_timestamp&order=desc';
					break;
				}
				//$('.reward-item-list').load(base_url+'settings/page_reward/view/'+page_id+query+' .reward-item-list>*', trigger_countdown);
				get_reward_list();
			}
			return false;
		}
		
		get_reward_list = function() {
			set_loading();
			var tabhead = '';
			var element = $('.reward-item-list');
			if($('.tab-head').length == 0) {
				element = $('.main-content');
				tabhead = '&tabhead=true';
			}
			element.load(base_url+'tab/redeem_list/'+page_id+query+tabhead,function(){
				$('.tab.sort').unbind('click').click(sort_reward);
				view_reward();
			});
		}

		$('.main-content').empty();
		get_reward_list();
	}

	load_page_activities = function () {
		
		var activities_per_page = 10;

		filter_page_activities = function (page_index,jq){
			set_loading();
			$.get(base_url+'tab/activities/'+page_id+'/'+activities_per_page+'/'+(page_index * activities_per_page), function(result) {
				$('.list-recent-activity').html(result);
				trigger_timeago();
			});
			check_pagination('.pagination-activity');
		}
		
		$('.main-content').html('<div class="list-recent-activity"></div><div class="pagination-activity strip"></div>')
		$.get(base_url+'tab/json_count_page_activities/'+page_id, function(count) {
			$('.pagination-activity').pagination(count, {
				items_per_page:activities_per_page,
				callback:filter_page_activities,
				load_first_page:true,
				next_text:null,
				prev_text:null
			});
		});
	}

	load_my_dashboard = function (){
		set_loading();
		$('div#main').load(base_url+'tab/profile/'+page_id+'/'+token,function(){
			main_menu();
			campaign_box();
			//reward_box(); //wishlish
			activity_box();
		});
	}

	load_my_activities = function () {
		set_loading();
		$('.main-content').html('<div class="list-recent-activity"></div><div class="pagination-activity strip"></div>')
		$('.list-recent-activity').load(base_url+'tab/activities/'+page_id,function(){
			activity_box();
		});
	}

	load_my_badges = function () {
		
		var pages_per_page = 7;

		badge_pagination = function (){
			$('.main-content').html('<div class="pagination-badge strip"></div>');
			$.get(base_url+'tab/json_count_user_pages/'+user_id, function(user_pages_count) {
				$('.pagination-badge').pagination(user_pages_count, {
					items_per_page:pages_per_page,
					callback:get_user_badges,
					load_first_page:true,
					next_text:null,
					prev_text:null
				});
			});
			
			
		}

		get_user_badges = function (page_index,jq){
			set_loading();
			var url = base_url+'tab/user_badges/'+pages_per_page+'/'+(page_index * pages_per_page);
			$.get(url, function(result) {
				if($('.user-badges-list').length > 0)
				{
					$('.user-badges-list').replaceWith(result);
				} else {
					$('.main-content').prepend(result);
				}
				$('.next').click(load_page_badges);
			});
			check_pagination('.pagination-badge');
		}

		badge_pagination();
	}

	load_page_badges = function () {
		var page_id = $(this).attr('data-page-id');
		var badges_per_page = 7;
		$('.user-badges-list').empty().attr('class', 'page-badges-list');

		badge_pagination = function (){
			$.get(base_url+'tab/json_count_page_badges/'+page_id, function(page_badges_count) {
				$('.pagination-badge').pagination(page_badges_count, {
					items_per_page:badges_per_page,
					callback:get_page_badges,
					load_first_page:true,
					next_text:null,
					prev_text:null
				});
			});
		}

		get_page_badges = function (page_index,jq){
			set_loading();
			var url = base_url+'tab/page_badges/'+page_id+'/'+badges_per_page+'/'+(page_index * badges_per_page);
			if($('.page-badges-header').length == 0) url += '?header=true';
			$.get(url, function(result) {
				$('.page-badges-list').replaceWith(result);
				$('.back-to-user-badges').click(load_my_badges);
			});
			check_pagination('.pagination-badge');
		}

		badge_pagination();
	}

	trigger_timeago = function (argument) {
		$.each( $('.timeago'), function(index, span) { 
			$('.timeago').eq(index).text($.timeago(new Date(parseInt( $(span).text() , 10) * 1000)));
		});
	}

	trigger_countdown = function (){
		$('.end-time-countdown').each(function(){
			end_time = Date.createFromMysql($(this).text());
			$(this).countdown({
				until: end_time,
				format: 'DHMS',
				layout: '{dn}days {hnn}h {sep} {mnn}m {sep} {snn}s'});
		});
	};

	check_pagination = function (element) {
		if($(element).find('.pagination a').length == 0) {
			$(element).find('.pagination').remove();
		} else {
			if($(element).find('.pagination a').attr('href') == '#') { 
				$(element).find('.pagination a').removeAttr('href'); // Remove href="#"
			}
		}
	}

	campaign_box = function () {

		campaign_pagination =function () {
			var filter = $('.campaign-box .tab.active').attr('data-filter');
			var url_count = '';
			switch(filter){
				case 'me' : url_count = base_url+"page/json_count_user_campaigns/"+user_id+'/'+page_id; break;
				case 'me-active' : url_count = base_url+"page/json_count_active_user_campaigns/"+user_id+'/'+page_id; break;
				case 'me-expired' : url_count = base_url+"page/json_count_expired_user_campaigns/"+user_id+'/'+page_id; break;
				case 'active' : url_count = base_url+"tab/json_count_active_campaigns/"+page_id; break;
				case 'expired' : url_count = base_url+"tab/json_count_expired_campaigns/"+page_id; break;
				default : url_count = base_url+"tab/json_count_campaigns/"+page_id; break;
			}
			$.getJSON(url_count,function(campaign_count){
				$('.pagination-campaign').pagination(campaign_count, {
					items_per_page:per_page,
					callback:filter_campaigns,
					load_first_page:true,
					next_text:null,
					prev_text:null
				});
			});
		}
				
		filter_campaigns = function (page_index,jq){
			var filter = $('.campaign-box .tab.active').attr('data-filter');
			var url = base_url+'tab/campaigns/'+page_id+'/'+per_page+'/'+(page_index * per_page) + '?filter='+filter +'&viewas='+ view_as;
			console.log(url);
			$('.list-campaign').load(url,trigger_countdown);
			check_pagination('.pagination-campaign');
		}

		$('.campaign-box .tab').click(function(){
			set_loading();
			$(this).addClass('active').siblings().removeClass('active');
			campaign_pagination();
		}).eq(0).click();
	}

	activity_box = function () {

		get_filter = function () {
			return $('.activity-box .tab.active').attr('data-filter');
		}

		activitiy_pagination = function (){
			var filter = get_filter();
			$.get(base_url+'tab/json_count_page_activities/'+page_id+'?filter='+filter, function(count) {
				$('.pagination-activity').pagination(count, {
					items_per_page:per_page,
					callback:filter_activities,
					load_first_page:true,
					next_text:null,
					prev_text:null
				});
			});
		}

		filter_activities = function (page_index,jq){
			var filter = get_filter();
			set_loading();
			$.get(base_url+'tab/activities/'+page_id+'/'+per_page+'/'+(page_index * per_page)+'?filter='+filter, function(result) {
				$('.list-recent-activity').html(result);
				trigger_timeago();
			});
			check_pagination('.pagination-activity');
		}

		$('.activity-box .tab').click(function(){
			$(this).addClass('active').siblings().removeClass('active');
			activitiy_pagination();
		});

		$('.activity-box .tab.active').click();
	}

	reward_box = function () {

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

		filter_reward = function (page_index,jq){
			$('.list-reward').find('.reward-item').hide().eq(page_index).show();
			check_pagination('.pagination-reward');
			trigger_countdown();
			view_reward();
		}

		$('.reward-box .tab').click(function(){
			set_loading();
			var filter = '?filter='+ $(this).attr('data-filter');
			$('.list-reward').load(base_url+'tab/redeem_list/'+page_id+'/'+filter, reward_pagination);
			$(this).addClass('active').siblings().removeClass('active');
		}).eq(0).click();

		//if($('.get-this-reward').length>0) get_reward();
		
		trigger_countdown();
	}

	view_reward = function () {
		$('.view-reward-detail').click(function() {
			$.fancybox({
				href: $(this).attr('href'),
				onComplete: confirm_redeem
			});
			return false;
		});

		confirm_redeem = function () {
			trigger_countdown();
			$('.get-this-reward').click(function() {
				$('.point-cal').hide();
				$('.terms-and-conditions-box').show();
				$('input[name="agree-term"]').click(function () {
					if ( $(this).is(':checked') ) $('.confirm-get-this-reward').removeClass('inactive').addClass('green');
					else $('.confirm-get-this-reward').removeClass('green').addClass('inactive');
				});
				return false;	
			});
				
			$('.confirm-get-this-reward').click(function() {
				if($(this).hasClass('inactive')) return false;
				$.fancybox({
					href: $(this).attr('href'),
					onComplete: redeem_success
				});
				return false;
			});
			$('.btn.cancel').click(function(){
				$.fancybox.close();
				return false;
			});
		}

		redeem_success = function () {
			$('.btn.share-to-fb').click(function(){
				FB.ui(
					{
						method: 'feed',
						name: $(this).attr('data-name'),
						link: $(this).attr('href'),
						picture: $(this).attr('data-picture'),
						caption: $(this).attr('data-caption'),
						description: $(this).attr('data-description')
					},
					function(response) {
						if (response && response.post_id) {
							$.fancybox.close();
						} else {
							console.log('Post was not published.');
						}
					}
				);
				return false;
			});
			$('.btn.close').click(function(){
				$.fancybox.close();
				return false;
			});
		}
	}

	load_notification = function (){
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
	
	get_user_notifications = function (page_index) {
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
		load_page_dashboard();
		return false;
	});

	$('a.a-profile').live('click',function(){
		load_my_dashboard();
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
		default : load_page_dashboard();
	}
	
});