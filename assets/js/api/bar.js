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
sh_signup = function(fb_access_token){
	(function($){
		
		$.fancybox({
			href: base_url+'tab/signup/'+page_id+'/'+app_install_id+'?facebook_access_token='+fb_access_token
		});
		// $('form.signup-form').die('submit');
		// $('form.signup-form').live('submit', function() {
			// $(this).ajaxSubmit({target:'#signup-form'});
			// return false;
		// });
		
		$('div.popup-fb.signup').live('keyup mousemove', function(){
			var this_popup = $(this);
			var complete = true;
			$.each( $('form.signup-form input[type="text"]', this_popup), function () {
				if( $(this).val() == '') complete = false;
			});
			if(complete) $('a.bt-next-inactive').attr('class', 'bt-next');
			else $('a.bt-next').attr('class', 'bt-next-inactive');
		});
		
		$('a.bt-next').live('click', function(){
			// $('form.signup-form').ajaxSubmit({
				// target:'.popup-fb.signup',
				// replaceTarget:true,
				// dataType: 'jsonp',
				// success:function(response){
					// console.log($(response));
					// if($(response).is('.popup-fb.signup-page')){ //Success signup sh, going to signup-page
						// applyOptionsToPageSignup();
					// }
				// }
			// });
			// return false;
			
			$('form.signup-form').unbind('submit').submit(function() {
				  var url = $(this).attr('action');
				  var params = $(this).serialize();
				  $.getJSON(url + '?' + params + "&callback=?", function(data) {
					// console.log(data);
					// success
					if(data.status == 'error'){
						console.log('error'); //TODO : display error message
					} else if(data.status == 'ok'){
						sh_signup_page(fb_access_token);
					}
				  })
				  return false
			}).submit();
			
		});
	})(jQuery);
}

sh_signup_page = function(fb_access_token){
	(function($){			
		$('div.popup-fb.signup-page #policy').die('click').live('click', function() {
			if ( $('#policy:checked').length > 0 ) $('a.bt-done-inactive').attr('class', 'bt-done');
			else $('a.bt-done').attr('class', 'bt-done-inactive');
		});
			
		$('div.popup-fb.signup-page a.bt-done').die('click').live('click',function(){
			// $('.signup-form').ajaxSubmit({
				// target:'div.popup-fb.signup-page',
				// replaceTarget: true,
				// dataType: 'jsonp'
			// });
			
			$('form.signup-form').unbind('submit').submit(function() {
				  var url = $(this).attr('action');
				  var params = $(this).serialize();
				  $.getJSON(url + '?' + params + "&callback=?", function(data) {
					console.log(data);
					// success
					if(data.status == 'error'){
						console.log('error'); //TODO : display error message
					} else if(data.status == 'ok'){
						console.log(data.redirect_url);
						sh_signup_complete(data.redirect_url);
					}
				  })
				  return false
			}).submit();
		});
		
		if(app_mode){
			$.fancybox({
				href: base_url+'tab/signup_page/'+page_id+'/'+app_install_id+'?facebook_access_token='+fb_access_token,
				modal: true,
				onComplete: applyOptionsToPageSignup
			});
		} else {
			$.fancybox({
				href: base_url+'tab/signup_page/'+page_id+'?facebook_access_token='+fb_access_token,
				modal: true,
				onComplete: applyOptionsToPageSignup
			});
		}
	})(jQuery);
}

sh_signup_complete = function(redirect_url){
	(function($){
		console.log(encodeURIComponent(redirect_url));
		$.fancybox({
			href: base_url+'tab/signup_complete?next=' + encodeURIComponent(redirect_url),
			modal: true
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
	getScript(base_url+'assets/js/common/jquery-ui.min.js', 'jQuery.ui', function(){
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
			
			$('div.popup-fb a.bt-start').live('click',function(){
				$.fancybox.close();
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
				$('li.notification a.amount span').hide();
				$('ul.notification_list_bar li').not('li.last-child').remove();
			  // if hide, fetch data
			  if(!fetching_notification && $('li.notification').hasClass('active')){
			    fetching_notification = true;
  				$.get(base_url + '/api/show_notification?user_id='+user_id, function(result){
  					if(result.notification_list){
  						var notification_list = result.notification_list;
  						var template = $('<li class="separator">'+
																	'<a style="display: none;">'+
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
  								if( $('ul.notification_list_bar li').not('li.last-child').length < 5 ) { // Show only 5 latest notifications
									var li = template.clone();
									notification_list[i].read ? '' : li.addClass('unread');
									li.find('a').attr('href', notification_list[i].link);
									li.find('p.message').html(notification_list[i].message);
									li.find('p.time').html($.timeago(new Date(parseInt(notification_list[i].timestamp, 10) * 1000)));
									li.find('img').attr('src', notification_list[i].image);
									li.show();
									$('ul.notification_list_bar').prepend(li);
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
				sh_guest();
			}
		}
		
	})(jQuery);
};
 
applyOptionsToPageSignup = function(){ //console.log('applyOptionsToPageSignup invoked');
	(function($){
		if(view_as == 'user'){
			$('div#signup-form').prepend('You\'re already a SocialHappen user, please signup to this page');
		}
		
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

close_popup = function(){
	(function($){
		$.fancybox.close();
	})(jQuery);
};

getScript(base_url + 'assets/js/common/jquery.min.js', 'jQuery', loadChildScripts);

/* 
 * a backwards compatable implementation of postMessage
 * by Josh Fraser (joshfraser.com)
 * released under the Apache 2.0 license.  
 *
 * this code was adapted from Ben Alman's jQuery postMessage code found at:
 * http://benalman.com/projects/jquery-postmessage-plugin/
 * 
 * other inspiration was taken from Luke Shepard's code for Facebook Connect:
 * http://github.com/facebook/connect-js/blob/master/src/core/xd.js
 *
 * the goal of this project was to make a backwards compatable version of postMessage
 * without having any dependency on jQuery or the FB Connect libraries
 *
 * my goal was to keep this as terse as possible since my own purpose was to use this 
 * as part of a distributed widget where filesize could be sensative.
 * 
 */

// everything is wrapped in the XD function to reduce namespace collisions
var XD = function(){
  
    var interval_id,
    last_hash,
    cache_bust = 1,
    attached_callback,
    window = this;
    
    return {
        // postMessage : function(message, target_url, target) {
            
            // if (!target_url) { 
                // return; 
            // }
    
            // target = target || parent;  // default to parent
    
            // if (window['postMessage']) {
                // the browser supports window.postMessage, so call it with a targetOrigin
                // set appropriately, based on the target_url parameter.
                // target['postMessage'](message, target_url.replace( /([^:]+:\/\/[^\/]+).*/, '$1'));

            // } else if (target_url) {
                // the browser does not support window.postMessage, so set the location
                // of the target to target_url#message. A bit ugly, but it works! A cache
                // bust parameter is added to ensure that repeat messages trigger the callback.
                // target.location = target_url.replace(/#.*$/, '') + '#' + (+new Date) + (cache_bust++) + '&' + message;
            // }
        // },
		
  
        receiveMessage : function(callback, source_origin) {
            
            // browser supports window.postMessage
            if (window['postMessage']) {
                // bind the callback to the actual event associated with window.postMessage
                if (callback) {
                    attached_callback = function(e) { //console.log('source_origin',source_origin,'e.origin',e.origin);
                        if ((typeof source_origin === 'string' && e.origin !== source_origin)
                        || (Object.prototype.toString.call(source_origin) === "[object Function]" && source_origin(e.origin) === !1)) {
                            return !1;
                        }
                        callback(e);
                    };
                }
                if (window['addEventListener']) {
                    window[callback ? 'addEventListener' : 'removeEventListener']('message', attached_callback, !1);
                } else {
                    window[callback ? 'attachEvent' : 'detachEvent']('onmessage', attached_callback);
                }
            } else {
                // a polling loop is started & callback is called whenever the location.hash changes
                interval_id && clearInterval(interval_id);
                interval_id = null;

                if (callback) {
                    interval_id = setInterval(function(){
                        var hash = document.location.hash,
                        re = /^#?\d+&/;
                        if (hash !== last_hash && re.test(hash)) {
                            last_hash = hash;
                            callback({data: hash.replace(re, '')});
                        }
                    }, 100);
                }
            }   
        }
    };
}();

XD.receiveMessage(function(message){ // Receives data from child iframe
	if(message.data.sh_message == "logged in"){
		if(view_as == 'guest' || is_user_register_to_page) {
			sh_signup(message.data.fb_access_token);
		} else {
			sh_signup_page(message.data.fb_access_token);
		} 
	}
}, sh_domain);
