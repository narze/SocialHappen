$(function(){

	if($('.navbar').length > 0) //For new bar (bootstrap)
	{
		var fetching_notification = false;
		

		$('li.notification a').click(toggleNotification_in_bootstrap);

		loadCompany_in_bootstrap();

		function loadCompany_in_bootstrap () {
			var template = $('.companies li:first-child');
			var create_company = $('.companies li.create-company');
			$('.companies').empty();
			for(i in user_companies){
				$.ajax({
					url: base_url+'company/json_get_profile/' + user_companies[i].company_id,
					dataType: 'json',
					async: false,
					success: function(data){
						var li = template.clone();
						var page_template = li.find('.page-name');
						li.find('.page-name').remove();
						var no_page = li.find('p.no-page');

						li.find('.company-image').attr('src', imgsize(data.company_image,'square'));
						li.find('.company-name').find('a').attr('href', base_url+'company/'+data.company_id).text(data.company_name);
						$.getJSON(base_url+'company/json_get_pages/' + user_companies[i].company_id, function(pages) {
							if(pages.length>0) {
								$.each(pages, function(i,page){
									var p = page_template.clone(); 
									p.find('a').attr('href', base_url+'page/'+page.page_id).text(page.page_name);
									li.append(p);
									no_page.remove();
								});
							} else {
								no_page.find('a').attr('href', base_url+'company/'+user_companies[i].company_id);
								li.append( no_page );
							}
						});
						$('.companies').append(li);
					}
				});
			}
			if(create_company.length > 0) {

				$('.companies').append(create_company);

				function create_company_submit () {
					$('.create-company-submit').click(function(){
						$('#create-company-form').die('submit');
						$('#create-company-form').ajaxSubmit({
							success: function (result) {
								var success = $(result).find('.alert-success');
								$('.modal-body').replaceWith($(result).find('.modal-body'));
								if(success.length > 0) {
									//console.log(success);
									window.location = success.attr('data-redirect');
								} else {
									create_company_submit();
								}
							}
						});
					});
				}

				$('.bt-create_company').click(function(){
					$.get(base_url+'bar/create_company_bootstrap', function(popup){
						popup = $(popup);
						$('body').append(popup.hide());
						popup.modal('show');
						create_company_submit();
					});
					return false;
				});
			}
		}

		function toggleNotification_in_bootstrap(){
			$('li.notification .amount span').hide();
			$('ul.notification_list_bar li').not('li.all-notification').remove();

			var notification_box = $('li.notification')

			// if hide, fetch data
			if(fetching_notification == false && notification_box.hasClass('open') == false){
				fetching_notification = true;
				$.get(base_url + '/api/show_notification?user_id='+user_id, function(result){
					if(result.notification_list){
						var notification_list = result.notification_list;
						var template = $('<li class="separator">'+
							'<a>'+
								'<img src="" />'+
								'<p class="message"></p>'+
								'<p class="time"></p>'+
							'</a>'+
						'</li>');
						if(notification_list.length > 0){
							var notification_id_list = [];
							$('ul.notification_list_bar li').not('li.all-notification').remove();
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
								li.show();
								$('ul.notification_list_bar').prepend(li);
								if( $('ul.notification_list_bar li').not('li.all-notification').length > 5 ) { // Show only 5 latest notifications
									$('ul.notification_list_bar li.all-notification').prev().remove();
								}
							}
							$.get(base_url + '/api/read_notification?user_id='+user_id+'&notification_list='+JSON.stringify(notification_id_list), function(result){
								
							}, 'json');
							
							$('ul.notification_list_bar a').show();
						} else {
							template.hide();
							if($('li.notification').hasClass('open')){
								console.log('hasClass open');
								$('ul.notification_list_bar li').not('li.all-notification').remove();
								var template = $('<li class="no-notification"><p>No notification.</p></li>');
								$('ul.notification_list_bar').prepend(template);
								$('ul.notification_list_bar').show();
							}
						}
					}
					fetching_notification = false;
				}, 'json');
			} else { 
				//Do nothing
			}
		}
	}
	else //For old bar
	{

		loadCompany();

		var fetching_notification = false;
	
		$('.toggle').live('click',function(){
			$('.toggle').not(this).removeClass('active').find('ul').hide();
			$(this).toggleClass('active').find('ul').toggle();
		});
		
		$('.toggle ul li a').live('click',function(){
			$('.toggle ul').hide();
		});
		
		$('li.notification').live('click', toggleNotification);
		
		var mouse_is_inside = false;
		$('.toggle').hover(function(){ 
			mouse_is_inside=true;
		}, function(){ 
			mouse_is_inside=false;
		});

		$("body").mouseup(function(){
			if(! mouse_is_inside) $('.toggle').removeClass('active').find('ul').hide();
		});
		
		$('.notice a.close').live('click', function() {
			$(this).parent().hide();
		});

		function loadCompany () {
			var template = $('.goto ul li:first-child');
			var create_company = $('.goto ul li.create-company');
			$('.goto ul').empty();
			for(i in user_companies){
				$.ajax({
					url: base_url+'company/json_get_profile/' + user_companies[i].company_id,
					dataType: 'json',
					async: false,
					success: function(data){
						var li = template.clone();
						var page_template = li.find('p.pagename');
						var no_page = li.find('p.no-page');
						li.find('p').remove();
						li.find('img').attr('src', imgsize(data.company_image,'square'));
						li.find('h2 a').attr('href', base_url+'company/'+data.company_id).text(data.company_name);
						$.getJSON(base_url+'company/json_get_pages/' + user_companies[i].company_id, function(pages) {
							if(pages.length>0) {
								$.each(pages, function(i,page){
									var p = page_template.clone(); 
									p.find('a').attr('href', base_url+'page/'+page.page_id);
									p.find('a').text(page.page_name);
									li.append(p);
								});
							} else {
								no_page.find('a').attr('href', base_url+'company/'+user_companies[i].company_id);
								li.append( no_page );
							}
						});
						$('.goto ul').append(li);
					}
				});
			}
			if(create_company.length > 0) $('.goto ul').append(create_company);
			$('.goto ul li:last-child').addClass('last-child');
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

		

		function toggleNotification()
		{
			$('li.notification a.amount span').hide();
			$('ul.notification_list_bar li').not('li.last-child').remove();
			  // if hide, fetch data
			  if(!fetching_notification && $('li.notification').hasClass('active')){
			    fetching_notification = true;
  				$.get(base_url + '/api/show_notification?user_id='+user_id, function(result){
  					if(result.notification_list){
  						var notification_list = result.notification_list;
  						var template = $('<li class="separator">'+
						'<a>'+
							'<img src="" />'+
							'<p class="message"></p>'+
							'<p class="time"></p>'+
						'</a>'+
					'</li>');
  						if(notification_list.length > 0){
  							var notification_id_list = [];
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
								li.show();
								$('ul.notification_list_bar').prepend(li);
								if( $('ul.notification_list_bar li').not('li.last-child').length > 5 ) { // Show only 5 latest notifications
									$('ul.notification_list_bar li.last-child').prev().remove();
								}
  							}
  							$.get(base_url + '/api/read_notification?user_id='+user_id+'&notification_list='+JSON.stringify(notification_id_list), function(result){
  								
  							}, 'json');
  							
  							$('ul.notification_list_bar a').show();
  						} else {
  							template.hide();
  							if($('li.notification').hasClass('active')){
  							  $('ul.notification_list_bar li').not('li.last-child').remove();
  							  var template = $('<li class="no-notification"><p>No notification.</p></li>');
                  $('ul.notification_list_bar').prepend(template);
                  $('ul.notification_list_bar').show();
                }
  						}
  						
  						if($('li.notification').hasClass('active')){
							  $('ul.notification_list_bar').show();
		  					$('ul.notification_list_bar li').show();
							}
					}
					
					fetching_notification = false;
				}, 'json');
				}else{ // if showing, hide it
				  $('ul.notification_list_bar').hide();
				}
		}


	}

});