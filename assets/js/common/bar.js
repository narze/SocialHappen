$(function(){
	
	$('.notificationtoggle').live('click', toggleNotification);
	
	$('.notice a.close').live('click', function() {
		$(this).parent().toggle();
	});

	for(i in user_companies){
		$.ajax({
			url: base_url+'company/json_get_profile/' + user_companies[i].company_id,
			dataType: 'json',
			async: false,
			success: function(data){
				var j = i;
				$('.goto div ul').append('<li class="goto-list-company-'+user_companies[i].company_id+'"><p class="thumb"><img class="company-image"  src="'+imgsize(data.company_image,'square')+'" alt="" /></p><h2><a href="'+base_url+'company/'+data.company_id+'">'+data.company_name+'</a></h2></li>');
				$.getJSON(base_url+'company/json_get_pages/' + user_companies[i].company_id, function(data) {
					if(data.length>0) {
						$.each(data, function(i,item){
							$('.goto-list-company-'+ item.company_id).append('<p class="goto-list-company-page-'+item.page_id+'">&raquo; <a href="'+base_url+'page/'+item.page_id+'">'+item.page_name+'</a></p>');
						});
					} else {
						$('.goto-list-company-'+ user_companies[j].company_id).append('<p>No page yet</p><p><a href="'+base_url+'company/'+user_companies[j].company_id+'">+ add new page</a></p>');
					}
				});
			}
		});
	}
	
	var companyname;
	var companydetail;

	function label() {
		
		//Company name
		companyname = $('#company_name').val('Company name').addClass('inactive');
		companyname.focus(function () {
			if($(this).val() == 'Company name') $(this).val('').removeClass('inactive');
		});
		companyname.blur(function () {
			if($(this).val() == '') $(this).val('Company name').addClass('inactive');
		});
		
		//Company detail
		companydetail = $('#company_detail').val('Company detail').addClass('inactive');
		companydetail.focus(function () {
			if($(this).val() == 'Company detail') $(this).val('').removeClass('inactive');
		});
		companydetail.blur(function () {
			if($(this).val() == '') $(this).val('Company detail').addClass('inactive');
		});
		
	}
	
	$('#create_company, .bt-create_company').live('click',function(){
		$.fancybox({
			href: base_url+'bar/create_company',
			transitionIn: 'elastic',
			transitionOut: 'elastic',
			padding: 0,
			scrolling: 'no',
			onComplete: label
		});
		return false;
	});
	
	$('#create-company a.bt-continue').live('click',function(){
		
		if($('#company_name').attr('class') == 'inactive') $('#company_name').val('');
		if($('#company_detail').attr('class') == 'inactive') $('#company_detail').val('');
		
		$('form.create-company-form').die('submit');
		$('form.create-company-form').ajaxSubmit({
			target:'div.popup_create-company',
			replaceTarget:true,
			success:label});
		return false;
	});
	
	function toggleNotification(){
		$('.notificationtoggle').not(this).find('ul').hide();
		$('.toggle').not(this).find('ul').hide();
		$.get(base_url + '/api/show_notification?user_id='+user_id, function(result){
			if(result.notification_list){
				var notification_list = result.notification_list;
				if(notification_list.length > 0){
					var notification_id_list = [];
					var template = $('ul.notification_list_bar li:first-child');
					$('ul.notification_list_bar li').not('li.last-child').remove();
					for(var i = notification_list.length - 1; i >= 0; i--){
						if(!notification_list[i].read){
							notification_id_list.push(notification_list[i]._id);
						}
						var li = template.clone();
						notification_list[i].read ? '' : li.addClass('unread');
						li.find('a').attr('href', notification_list[i].link);
						li.find('p.message').html(notification_list[i].message);
						li.find('p.time').html($.timeago(new Date(parseInt(notification_list[i].timestamp, 10) * 1000)));
						li.find('img').attr('src', notification_list[i].image);
						$('ul.notification_list_bar').prepend(li);
						if( $('ul.notification_list_bar li').not('li.last-child').length == 5 ) break; // Show only 5 latest notifications
					}
					$.get(base_url + '/api/read_notification?user_id='+user_id+'&notification_list='+JSON.stringify(notification_id_list), function(result){
						
					}, 'json');
				}
			}
		}, 'json');
		$(this).find('ul').toggle();
	}
});