sh_guest = function(){
	(function($){
		$.fancybox({
			href: base_url+'tab/guest'
		});
		$('a.bt-don-awesome').die('click');
		$('a.bt-don-awesome').live('click',function(){
			$.fancybox.close();
		});
	})(jQuery);
}
sh_register = function(){
	(function($){
		$.fancybox({
			href: base_url+'tab/signup/'+page_id
		});
		$('form.signup-form').die('submit');
		$('form.signup-form').live('submit', function() {
			$(this).ajaxSubmit({target:'#signup-form'});
			return false;
		});
		
		$('form.signup-form input').live('keyup', function(){
			
			var complete = true;
			$.each( $('form.signup-form input[type="text"]'), function () {
				if( $(this).val() == '') complete = false;
			});
			if(complete) $('a.bt-next-inactive').attr('class', 'bt-next');
			else $('a.bt-next').attr('class', 'bt-next-inactive');
		});
		
		$('a.bt-next').live('click', function(){
			$('form.signup-form').ajaxSubmit({target:'.popup-fb.signup', replaceTarget:true});
			return false;
		});
	})(jQuery);
}
	
getScript = function(url, checkName, success) {
	if(eval('typeof '+checkName+' == "function"') || eval('typeof '+checkName+' == "object"')) { //check if script is already loaded
		success(); //don't reload
	} else {
		var script     = document.createElement('script');
			 script.src = url;
		var head = document.getElementsByTagName('head')[0],
		done = false;

		// Attach handlers for all browsers
		script.onload = script.onreadystatechange = function() {
				if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
				done = true;
						// callback function provided as param
						success();

						script.onload = script.onreadystatechange = null;
						head.removeChild(script);
				};
		};
		head.appendChild(script);
	}
};

loadChildScripts = function(){
	getScript(base_url+'assets/js/common/jquery.form.js', 'jQuery.fn.ajaxForm', function(){
		getScript(base_url + 'assets/js/common/fancybox/jquery.fancybox-1.3.4.js', 'jQuery.fancybox', function(){
			onLoad();
		});
	});
}

onLoad = function(){
	(function($){
		$(function(){
			$('.toggle').live('click',function(){
			  $('.notificationtoggle').not(this).find('ul').hide();
				$('.toggle').not(this).find('ul').hide();
				$(this).find('ul').toggle();
			});
			
			$('.notificationtoggle').live('click',function(){
			  $('.notificationtoggle').not(this).find('ul').hide();
		  $('.toggle').not(this).find('ul').hide();
		  $.get(base_url + '/api/show_notification?user_id='+user_id, function(result){
			if(result.notification_list){
			  var notification_list = result.notification_list;
			  if(notification_list.length > 0){
				var notification_id_list = [];
				
				$('ul.notification_list_bar li').not('li.last-child').remove();
				for(var i = notification_list.length - 1; i >= 0; i--){
				  if(!notification_list[i].read){
					notification_id_list.push(notification_list[i]._id);
				  }
				  
				  var unread = notification_list[i].read ? '' : 'unread';
				  $('ul.notification_list_bar').prepend('<li class="separator '+unread+'"><a href="'+notification_list[i].link+'">'+notification_list[i].message+'</a></li>');
				}
				
				$.get(base_url + '/api/read_notification?user_id='+user_id+'&notification_list='+JSON.stringify(notification_id_list), function(result){

				}, 'json');
			  }
			}
		  }, 'json');
		  $(this).find('ul').toggle();
		});
				
			var mouse_is_inside = false;
			$('.toggle, .notificationtoggle').hover(function(){ 
				mouse_is_inside=true;
			}, function(){ 
				mouse_is_inside=false;
			});

			$("body").mouseup(function(){
				if(! mouse_is_inside) $('.toggle, .notificationtoggle').find('ul').hide();
			});
			
			sh_popup();
		});
	})(jQuery);
};

sh_popup = function(){
	(function($){
		if(view_as == 'guest'){ //@TODO : User should not see view_as, let's decide it server-side
			sh_guest();
		} else if(view_as == 'admin'){
			if(page_app_installed_id!=0) {
				$.fancybox({
					href: base_url+'tab/app_installed/'+ page_app_installed_id
				});
				$('a.bt-stay_fb').die('click');
				$('a.bt-stay_fb').live('click',function(){
					$.fancybox.close();
				});
				page_app_installed_id=0;
			} else if(page_installed==0){
				$.fancybox({
					href: base_url+'tab/page_installed/'+ page_id
				});
				$('a.bt-stay_fb').die('click');
				$('a.bt-stay_fb').live('click',function(){
					$.fancybox.close();
				});
				page_installed=1;
			}
		} else {
			if(!is_user_register_to_page) {
				$.fancybox({
					href: base_url+'tab/signup_page/'+page_id,
					modal: true
				});
				
				$('#policy').live('click', function() {
					if ( $('#policy:checked').length > 0 ) $('a.bt-done-inactive').attr('class', 'bt-done');
					else $('a.bt-done').attr('class', 'bt-done-inactive');
				});
				
				$('a.bt-done').die('click');
				$('a.bt-done').live('click',function(){
					$('.signup-form').ajaxSubmit({
						target:'div.popup-fb.signup-page',
						replaceTarget: true
					});
				});
			}
		}
	})(jQuery);
 }
 
 


	getScript('http://code.jquery.com/jquery-latest.min.js', 'jQuery', loadChildScripts);
