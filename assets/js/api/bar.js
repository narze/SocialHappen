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
			href: base_url+'tab/signup/'+page_id+'/'+app_install_id
		});
		$('form.signup-form').die('submit');
		$('form.signup-form').live('submit', function() {
			$(this).ajaxSubmit({target:'#signup-form'});
			return false;
		});
		
		$('div.popup-fb.signup').live('keyup mousemove', function(){
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
	getScript('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', 'jQuery.ui', function(){
		getScript(base_url+'assets/js/common/jquery.form.js', 'jQuery.fn.ajaxForm', function(){
			getScript(base_url + 'assets/js/common/fancybox/jquery.fancybox-1.3.4.js', 'jQuery.fancybox', function(){
				getScript(base_url + 'assets/js/common/jquery.timeago.js', 'jQuery.timeago', function(){
					onLoad();
				});
			});
		});
	});
}

onLoad = function(){
	(function($){
		$(function(){
		  var fetching_notification = false;
		  
			$('.toggle').live('click',function(){
				$('.toggle').not(this).removeClass('active').find('ul').hide();
				$(this).toggleClass('active').find('ul').toggle();
			});
			
			$('.toggle ul li a').live('click',function(){
				$('.toggle').removeClass('active').find('ul').hide();
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
			
			sh_popup();
			
			$('div.popup-fb.signup-page #policy').live('click', function() {
				if ( $('#policy:checked').length > 0 ) $('a.bt-done-inactive').attr('class', 'bt-done');
				else $('a.bt-done').attr('class', 'bt-done-inactive');
			});
				
			$('div.popup-fb.signup-page a.bt-done').die('click');
			$('div.popup-fb.signup-page a.bt-done').live('click',function(){
				$('.signup-form').ajaxSubmit({
					target:'div.popup-fb.signup-page',
					replaceTarget: true
				});
			});
			
			$('div.popup-fb a.bt-start').live('click',function(){
				$.fancybox.close();
				// window.parent.location.reload(); //Error - Unsafe JavaScript attempt to access frame
				// window.top.location.href = window.top.location.href;
				// console.log(window.top.location.href);
			});
			
			$('div.popup-fb a.bt-cancel').live('click',function(){
        $.fancybox.close();
      });
			
			$('a.a-logout').live('click', function(){
				$.fancybox({
					href: base_url + 'tab/logout/'+page_id+'/'+app_install_id
				});
			});
			
			function toggleNotification(){
			  // if hide, fetch data
			  if(!fetching_notification && $('li.notification').hasClass('active')){
			    fetching_notification = true;
  				$.get(base_url + '/api/show_notification?user_id='+user_id, function(result){
  					if(result.notification_list){
  						var notification_list = result.notification_list;
  						var template = $('ul.notification_list_bar li:first-child');
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
  								if( $('ul.notification_list_bar li').not('li.last-child').length == 5 ) break; // Show only 5 latest notifications
  							}
  							$.get(base_url + '/api/read_notification?user_id='+user_id+'&notification_list='+JSON.stringify(notification_id_list), function(result){
  								
  							}, 'json');
  							
  							$('ul.notification_list_bar a').show();
  						} else {
  							template.hide();
  							if($('li.notification').hasClass('active')){
  							  $('ul.notification_list_bar li').not('li.last-child').remove();
  							  var li = template.clone();
  							  li.find('a').attr('href', '');
                  li.find('p.message').html('No notification.');
                  li.find('p.time').html('');
                  li.find('img').attr('src', '');
                  li.show();
                  $('ul.notification_list_bar').prepend(li);
                  $('ul.notification_list_bar').show();
                }
  						}
					}
					if($('li.notification').hasClass('active')){
					  $('ul.notification_list_bar').show();
  					$('ul.notification_list_bar li').show();
					}
					
					fetching_notification = false;
				}, 'json');
				}else{ // if showing, hide it
				  $('ul.notification_list_bar').hide();
				}
			}
		});
	})(jQuery);
};

sh_popup = function(){
	(function($){
		if(view_as == 'guest'){ //@TODO : User should not see view_as, let's decide it server-side
			$.fancybox({
				href: base_url+'tab/guest'
			});
			$('a.bt-don-awesome').die('click');
			$('a.bt-don-awesome').live('click',function(){
				$.fancybox.close();
			});
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
			  if(app_mode){
			    $.fancybox({
            href: base_url+'tab/signup_page/'+page_id+'/'+app_install_id,
            modal: true
          });
			  }else{
			    $.fancybox({
            href: base_url+'tab/signup_page/'+page_id,
            modal: true
          });
			  }
			}
		}
		
	})(jQuery);
};
 
applyOptionsToPageSignup = function(){
	(function($){
		$('#signup-form form div.form li[data-field-options]').each(function(){
			var textInput = $('div.inputs', this).find('input[type="text"], textarea'); //only texts & textareas
			var fieldOptions = $(this).data('fieldOptions');
			if(fieldOptions!=null){
				$.each(fieldOptions, function(i, val){
					console.log(i,val,textInput);
					if(typeof(i)=='number'){ //index is number, check value
						
					} else { //index is string, check it
						if(i=='calendar'){
							textInput.datepicker({ dateFormat: val });
						}
					}
				});
			}
		});
	})(jQuery);
};

getScript('http://code.jquery.com/jquery-latest.min.js', 'jQuery', loadChildScripts);
