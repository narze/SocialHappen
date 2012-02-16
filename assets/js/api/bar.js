 sh_guest = function(){
	(function($){
		jQuery.fancybox({
			href: base_url+'tab/guest',
			onComplete: function (){
				$('a.bt-don-awesome').on('click', function(){
					jQuery.fancybox.close();
				});
			}
		});
	})(sh_jq);
}
sh_signup = function(){
	(function($){
		jQuery.fancybox({
			href: base_url+'tab/signup/'+page_id+'/'+app_install_id+'?facebook_access_token='+facebook_access_token,
			onComplete: signup_form
		});
		
		function signup_form() {
			$('#fancybox-content').off()
				.on('keyup mousemove', 'div.popup-fb.signup', check_form)
				.on('click', 'a.bt-next', submit_form);

			function check_form(){
				var this_popup = $(this);
				var complete = true;
				$.each( $('form.signup-form input[type="text"]', this_popup), function () {
					if( $(this).val() == '') {
						complete = false;
					}
				});
				if(complete) {
					$('a.bt-next-inactive').attr('class', 'bt-next');
				} else {
					$('a.bt-next').attr('class', 'bt-next-inactive');
				}
			}

			function submit_form(){
				$('form.signup-form').unbind('submit').submit(function() {
					//fill user timezone into form
						var user_timezone = 'UTC';
						if(typeof jstz !== 'undefined'){
							user_timezone = jstz.determine_timezone().name();
						}
						$(this).find('input#timezone').val(user_timezone);
					//end

					  var url = $(this).attr('action');
					  var params = $(this).serialize();
					  $.getJSON(url + '?' + params + "&callback=?", function(data) {
						if(data.status == 'error'){
							if(data.error == 'verify'){
								sh_validate_error(data.error_messages);
							}
						} else if(data.status == 'ok'){
							user_name = $('input#first_name').val();
							user_image = $('div.profile img.profile').attr('src');
							sh_signup_page();
						}
					  })
					  return false
				}).submit();
			}
		}
		
	})(sh_jq);
}

sh_validate_error = function(error_messages){
	(function($){	
		var form_fields = $('form div.form>ul>li').removeClass('error');
		var form_labels = $('span.field-label', form_fields).show();
		$('span.error', form_fields).remove();
		for(var field_name in error_messages){
			var this_field = form_fields.filter('li[data-field-name="'+field_name+'"]');
			// console.log(field_name, error_messages[field_name]);
			$('span.field-label', this_field).hide();
			var field = this_field.addClass('error');
			$('<span/>').addClass('error').html(error_messages[field_name]).prependTo($('label.title', field));
			// console.log(field);
		}
	})(sh_jq);
}

sh_signup_page = function(){
	(function($){
		if(is_user_register_to_page){
			jQuery.fancybox({
				content: 'You have already registered to this page'
			});
		} else if(app_mode){
			jQuery.fancybox({
				href: base_url+'tab/signup_page/'+page_id+'/'+app_install_id+'?user_first_name='+user_name+'&user_image='+encodeURIComponent(user_image)+'&facebook_access_token='+facebook_access_token,
				modal: true,
				onComplete: signup_page_form
			});
		} else {
			jQuery.fancybox({
				href: base_url+'tab/signup_page/'+page_id+'?user_first_name='+user_name+'&user_image='+encodeURIComponent(user_image)+'&facebook_access_token='+facebook_access_token,
				modal: true,
				onComplete: signup_page_form
			});
		}

		function signup_page_form() { //console.log('signup_page_form invoked');
			$('#fancybox-content').off()
			.on('click', 'div.popup-fb.signup-page #policy', check_policy)
			.on('click', 'div.popup-fb.signup-page a.bt-done', submit_form);

			function check_policy() {
				if ( $('#policy:checked').length > 0 ) $('a.bt-done-inactive').attr('class', 'bt-done');
				else $('a.bt-done').attr('class', 'bt-done-inactive');
			}
				
			function submit_form(){
				$('form.signup-form').unbind('submit').submit(function() {
					  var url = $(this).attr('action');
					  var params = $(this).serialize();
					  $.getJSON(url + '?' + params + "&callback=?", function(data) {
						// console.log(data);
						// success
						if(data.status == 'error'){
							if(data.error == 'verify'){
								sh_validate_error(data.error_messages);
							}
						} else if(data.status == 'ok'){
							is_user_register_to_page = 1;
							sh_signup_complete(data.redirect_url);
						}
					  })
					  return false;
				}).submit();
			}

			if(view_as == 'user'){
				$('div#signup-form').prepend('You\'re already a SocialHappen user, please signup to this page');
			}
			
			$('#signup-form form div.form li[data-field-options]').each(function(){
				var textInput = jQuery('div.inputs', this).find('input[type="text"], textarea'); //only texts & textareas
				var fieldOptions = $(this).data('fieldOptions');
				if(fieldOptions!=null){
					$.each(fieldOptions, function(i, val){
						// console.log(i,val,textInput);
						if(typeof(i)=='number'){ //index is number, check value
							
						} else { //index is string, check it
							if(i=='calendar'){
								textInput.datepicker({ dateFormat: val });
							}
						}
					});
				}
			});
		};
	})(sh_jq);
}

sh_signup_complete = function(redirect_url){
	(function($){
		jQuery.fancybox({
			href: base_url+'tab/signup_complete?next=' + encodeURIComponent(redirect_url),
			modal: true
		});
	})(sh_jq);
}

sh_signup_campaign = function(){
	(function($){	
		if(is_user_register_to_campaign){
			jQuery.fancybox({
				content: 'You have already registered to this campaign'
			});
		} else if(app_mode){
			jQuery.fancybox({
				href: base_url+'tab/signup_campaign/'+app_install_id+'/'+campaign_id,
				onComplete: signup_campaign_form
			});

			function signup_campaign_form() { //console.log('signup_campaign_form invoked');
				$('#fancybox-content').off()
				.on('submit', 'div.popup-fb.signup-campaign form.signup-form', submit_form);

				function submit_form(){console.log('submit');
					$('form.signup-form').unbind('submit').submit(function() {
						  var url = $(this).attr('action');
						  var params = $(this).serialize();
						  $.getJSON(url + '?' + params + "&callback=?", function(data) {
							// console.log(data);
							// success
							// if(data.status == 'error'){
							// 	if(data.error == 'verify'){
							// 		sh_validate_error(data.error_messages);
							// 	}
							// } else if(data.status == 'ok'){
							// 	sh_signup_complete(data.redirect_url);
							// }
							if(data.status == 'ok'){
								is_user_register_to_campaign = 1;
								jQuery.fancybox.close();
							}
						  });
						  return false;
					}).submit();
					return false;
				}
			};
		}		
	})(sh_jq);
}

getScript = function(url, checkName, success) {
	if(checkName != 'jQuery' && (eval('typeof '+checkName+' == "function"') || eval('typeof '+checkName+' == "object"'))) { //check if script is already loaded
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
					
						if(checkName == 'jQuery'){
							
						}
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
	window.sh_jq = jQuery.noConflict(true); // jQuery -> old, sh_jq -> new
	getScript(base_url+'assets/js/common/jquery-ui.min.js', 'jQuery.ui', function(){
		getScript(base_url+'assets/js/common/jquery.form.js', 'jQuery.fn.ajaxForm', function(){
			getScript(base_url + 'assets/js/common/fancybox/jquery.fancybox-1.3.4.js', 'jQuery.fancybox', function(){
				getScript(base_url + 'assets/js/common/jquery.timeago.js', 'jQuery.timeago', function(){
	
				});
			});
		});
	});
}

loadNode = function(){
	(function($){
		$(function(){
			if(typeof io != 'undefined'){
				var session = 'SESSIONNAJA';
				var socket = io.connect(node_base_url);

				socket.on('connect', function(){
					console.log('send subscribe');
					socket.emit('subscribe', user_id, session);
				});

				socket.on('subscribeResult', function (data) {
					console.log('got subscribe result: ' + JSON.stringify(data));
				});

				socket.on('newNotificationAmount', function (notification_amount) {
					console.log('notification_amount: ' + notification_amount);
					if(notification_amount > 0){
						$('div.header ul.menu li.notification a.amount').html('').append('<span>').children('span').html(notification_amount);
					}else{
						$('div.header ul.menu li.notification a.amount').append('<span>').children('span').remove();
					}
				});
				
				socket.on('newNotificationMessage', function (notification_message) {
					console.log('notification_message: ' + JSON.stringify(notification_message));
				});
			}
		});
	})(sh_jq);
}

onLoad = function(){
	(function($){
		$(function(){
			loadNode();
			
		    var fetching_notification = false;
		  
		  	$('#sh-bar').off()
		  	.on('click','.toggle',function(){
				$('.toggle').not(this).removeClass('active').find('ul').hide();
				$(this).toggleClass('active').find('ul').toggle();
			})
			.on('click','.toggle ul li a',function(){
				$(this).parents('.toggle').toggleClass('active').find('ul').toggle();
			})
			.on('click','li.notification', toggleNotification)
			.on('click','a.a-logout', sh_logout)
			.on('click','a.app-config', sh_app_component);
			
			//App setting
			$('div.data ul.toggle').live('click', function(){
				$('.toggle').not(this).removeClass('active');
				$(this).addClass('active');
			})
			$('#app-setting-menu li a').live('click', function(){
				var elem = $(this);
				if(!elem.hasClass('active')){
					elem.parents('ul').find('a.active').removeClass('active');
					if(sh_canvas_config){
						if(elem.hasClass('config') && $('#app-config').length == 0){
							$('#app-content').empty();
							$('#app-config-temp').attr('id', 'app-config').appendTo('#app-content').show();					
						} else {
							$('#app-config').attr('id', 'app-config-temp').appendTo('body').hide();
						}
					} 
					if(!sh_canvas_config || !elem.hasClass('config')){
						var iframe = $('<iframe src="'+elem.attr('href')+'/?tab=true'+'" allowtransparency="true" frameborder="0" sandbox="allow-same-origin allow-forms allow-scripts" style="width:100%;height:100%;min-height:680px;"></iframe>');
						$('#app-content').html( '<div id="app-component-config"></div>' ).find('#app-component-config').append(iframe);
						$('#app-component-config').prepend('<h2 class="setting-title"><span>'+ elem.attr('title') +'</span></h2>');
					}

					elem.addClass('active');
				}
				return false;
			});

			unhover_hide();
			sh_popup();
			sh_sharebutton();
			sh_invitebutton();
			sh_test();

			$('div.popup-fb a.bt-start').live('click',function(){
				jQuery.fancybox.close();
			});
			
			$('div.popup-fb a.bt-cancel').live('click',function(){
		        jQuery.fancybox.close();
		    });
			
			// $().live('click', function(){
			// 	sh_logout();
			// 	jQuery.fancybox({
			// 		href: base_url + 'tab/logout/'+page_id+'/'+app_install_id
			// 	});
			// });

				
			function unhover_hide(){
				var mouse_is_inside = false;
				$('.toggle').hover(function(){ 
					mouse_is_inside = true;
				}, function(){ 
					mouse_is_inside = false;
				});
		
				$('body').hover(function(){ 
					$(this).mouseup(function(){
						if(! mouse_is_inside) $('.toggle').removeClass('active').find('ul').hide();
					});
				}, function(){ 
					$('.toggle').removeClass('active').find('ul').slideUp();
				});
			}
			
			function toggleNotification(){
				$('li.notification a.amount span').hide();
				$('ul.notification_list_bar li').not('li.last-child').remove();
			  // if hide, fetch data
			  if(!fetching_notification && $('li.notification').hasClass('active')){
			    fetching_notification = true;
  				$.get(base_url + 'api/show_notification?user_id='+user_id, function(result){
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
								var li = template.clone();
								notification_list[i].read ? '' : li.addClass('unread');
								li.find('a').attr('href', notification_list[i].link);
								li.find('p.message').html(notification_list[i].message);
								li.find('p.time').html(jQuery.timeago(new Date(parseInt(notification_list[i].timestamp, 10) * 1000)));
								li.find('img').attr('src', notification_list[i].image);
								li.show();
								$('ul.notification_list_bar').prepend(li);
								if( $('ul.notification_list_bar li').not('li.last-child').length > 5 ) { // Show only 5 latest notifications
									$('ul.notification_list_bar li.last-child').prev().remove();
								}
  							}
  							$.get(base_url + 'api/read_notification?user_id='+user_id+'&notification_list='+JSON.stringify(notification_id_list), function(result){
  								
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

			function sh_sharebutton(){
				$(document).on('click', '.sh-sharebutton', function(){
					if(view_as == 'guest'){
						sh_guest();
					} else {
						sh_sharebutton_menu($(this));
					}
					return false;
				});

				//DEBUG : remove this after test
				$('body').append('<div class="sh-sharebutton" data-href="" />'); // for debug

				function sh_sharebutton_menu(elem){
					var share_href = elem.data('href');

					if(share_href){
						share_href = encodeURIComponent(share_href);
					} else {
						share_href = encodeURIComponent(facebook_tab_url);
					}

					jQuery.fancybox({ // should use something better than fancybox?
						href:base_url+'share/'+app_install_id+'?link='+share_href,
						type:'iframe',
						transitionIn: 'elastic',
						transitionOut: 'elastic',
						padding: 0,
						// width: 908,
						height: 390, //TODO : height = 100% of inner content
						autoDimensions: false,
						scrolling: 'no',
						onStart: function() {
							//$("<style type='text/css'> #fancybox-wrap{ top:550px !important;} </style>").appendTo("head");
						}
					});
				}
			}

			function sh_invitebutton(){
				$(document).on('click', 'div.sh-invitebutton', function(){
					if(view_as == 'guest'){
						sh_guest();
					} else {
						sh_invitebutton_menu($(this));
					}
				});

				//DEBUG : remove this after test
				$('body').append('<div class="sh-invitebutton" data-href="" />'); // for debug

				function sh_invitebutton_menu(elem){
					jQuery.fancybox({ // should use something better than fancybox?
						href:base_url+'invite/'+app_install_id+'?facebook_page_id='+facebook_page_id,
						type:'iframe',
						transitionIn: 'elastic',
						transitionOut: 'elastic',
						padding: 0,
						// width: 908,
						// height: 518, //TODO : height = 100% of inner content
						autoDimensions: false,
						scrolling: 'no',
						onStart: function() {
							//$("<style type='text/css'> #fancybox-wrap{ top:550px !important;} </style>").appendTo("head");
						}
					});
				}
			}

			function sh_test(){ //this function is to test only, will be removed on production
				$('<div><a id="page_score" href="'+base_url+'tab/my_page_score/'+page_id+'">My Score</a></div>').appendTo('body');
				$('<div><a id="page_leaderboard" href="'+base_url+'tab/page_leaderboard/'+page_id+'">Page Leaderboard</a></div>').appendTo('body');
				$('<div><a id="page_reward" href="'+base_url+'tab/redeem_list/'+page_id+'?sort=start_timestamp&order=desc">Redeem Reward (Sort by start time)</a></div>').appendTo('body');
				$('<div><a class="page_reward_debug" href="'+base_url+'tab/redeem_list/'+page_id+'?sort=value&order=desc">Redeem Reward (Sort by value)</a></div>').appendTo('body');
				$('<div><a class="page_reward_debug" href="'+base_url+'tab/redeem_list/'+page_id+'?sort=status">Redeem Reward (Sort by status)</a></div>').appendTo('body');
				$('<div><a class="page_reward_debug" href="'+base_url+'tab/redeem_list/'+page_id+'?sort=redeem.point&order=desc">Redeem Reward (Sort by point)</a></div>').appendTo('body');
				var page_score = $('#page_score');
				var page_leaderboard = $('#page_leaderboard');
				var page_reward = $('#page_reward, .page_reward_debug');
				page_score.click(check_guest);
				page_leaderboard.click(check_guest);
				page_reward.click(check_guest);
				function check_guest(){
					if(view_as == 'guest'){
						sh_guest();
					} else {
						jQuery.fancybox({
							href:$(this).attr('href'),
							type:'iframe',
							transitionIn: 'elastic',
							transitionOut: 'elastic',
						});
					}
					return false;
				}
			}
		});
	})(sh_jq);
};

sh_app_component = function() {
	(function($){
		var app_config = $('a.app-config').attr('href');
		if(sh_canvas_config){
			top.location.href = app_config;
		} else {
			//load template
			$.ajax({
				async:true,
				type: 'GET',
				url: base_url+'api/setting_template',
				data: {
					app_id : app_id,
					app_secret_key : app_secret_key,
					app_install_id : app_install_id,
					app_install_secret_key : app_install_secret_key,
					user_id : user_id
				},
				success: function(result) {
					if(result.status == 'OK')
					{
						$('body>*').not(':has(#sh-bar)').remove();
						$('body').addClass('settings').append(result.html);
						//insert app config
						$('#app-setting-menu li a.config').click();
						$('#app-setting-template').show();
					}
					else
					{
						console.log( 'error loading template' );
					}
				},
				dataType: 'json'
			});
		}
	})(sh_jq);
	return false;
};

sh_popup = function(){
	(function($){
		if(view_as == 'guest'){ //@TODO : User should not see view_as, let's decide it server-side
			//sh_guest();
		} else if(view_as == 'admin'){ //page_app_installed_id = 1 //for test
			if(page_app_installed_id!=0) {
				/*
				jQuery.fancybox({
					href: base_url+'tab/app_installed/'+ page_app_installed_id
				});
				$('a.bt-stay_fb').die('click');
				$('a.bt-stay_fb').live('click',function(){
					jQuery.fancybox.close();
				});
				*/
				$.ajax({
					async:false,
					type: 'GET',
					url: base_url+'api/get_started',
					data: {
						app_id : app_id,
						app_secret_key : app_secret_key,
						app_install_id : app_install_id,
						app_install_secret_key : app_install_secret_key,
						user_id : user_id
					},
					success: function(result) {
						if(result.status == 'OK')
						{
							$('body>*').not(':has(#sh-bar)').remove();
							$('body').addClass('settings').append(result.html);
							$('a.app-config').live('click', sh_app_component);
						}
						else
						{
							console.log( result.message );
						}
					},
					dataType: 'json'
				});

				page_app_installed_id=0;
			} 
			// else if(page_installed==0){
				// jQuery.fancybox({
					// href: base_url+'tab/page_installed/'+ page_id
				// });
				// $('a.bt-stay_fb').die('click');
				// $('a.bt-stay_fb').live('click',function(){
					// jQuery.fancybox.close();
				// });
				// page_installed=1;
			// }
		} else {
			if(!is_user_register_to_page) {
				sh_signup_page();
			} else if(!is_user_register_to_campaign){
				sh_signup_campaign(); 
			}
		}
		
	})(sh_jq);
};

close_popup = function(){
	(function($){
		jQuery.fancybox.close();
	})(sh_jq);
};

sh_login = function(){
	XD.postMessage({sh_message:'login'}, base_url+'xd', document.getElementById('xd_sh').contentWindow);
}

sh_logout = function(){
	XD.postMessage({sh_message:'logout'}, base_url+'xd', document.getElementById('xd_sh').contentWindow);
}

sh_load_bar = function(){
	onLoad();
	console.log(view_as,user_id,page_id,app_install_id);
	jQuery.get(base_url+'xd/get_bar_content/'+view_as+'/'+user_id+'/'+facebook_user_id+'/'+page_id+'/'+app_install_id, function(data){
		sh_jq('div#sh-bar').html(data);
	});
}

sh_visit = function(){
	XD.postMessage({sh_message:'visit',sh_page_id:page_id,sh_app_install_id:app_install_id,sh_app_id:app_id}, base_url+'xd', document.getElementById('xd_sh').contentWindow);
}

// sh_get_role = function(){
// 	(function($){
// 		$.getJSON(base_url+'xd/get_role', function(){
// 			return
// 		});
// 	})(sh_jq);
// }

getScript(base_url + 'assets/js/common/jquery.min.js', 'jQuery', loadChildScripts);

/* 
 * a backwards compatable implementation of postMessage
 * by Josh Fraser (joshfraser.com)
 * released under the Apache 2.0 license.  
 *
 * this code was adapted from Ben Alman's sh_jq postMessage code found at:
 * http://benalman.com/projects/sh_jq-postmessage-plugin/
 * 
 * other inspiration was taken from Luke Shepard's code for Facebook Connect:
 * http://github.com/facebook/connect-js/blob/master/src/core/xd.js
 *
 * the goal of this project was to make a backwards compatable version of postMessage
 * without having any dependency on sh_jq or the FB Connect libraries
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
        postMessage : function(message, target_url, target) {
            
            if (!target_url) { 
                return; 
            }
    
            target = target || parent;  // default to parent
    
            if (window['postMessage']) {
                // the browser supports window.postMessage, so call it with a targetOrigin
                // set appropriately, based on the target_url parameter.
                target['postMessage'](message, target_url.replace( /([^:]+:\/\/[^\/]+).*/, '$1'));

            } else if (target_url) {
                // the browser does not support window.postMessage, so set the location
                // of the target to target_url#message. A bit ugly, but it works! A cache
                // bust parameter is added to ensure that repeat messages trigger the callback.
                target.location = target_url.replace(/#.*$/, '') + '#' + (+new Date) + (cache_bust++) + '&' + message;
            }
        },
		
  
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
	var data = message.data;
	var message = data.sh_message;
	var sh_login_status;
	// console.log(data);
	if(message === 'facebook_login_status'){ //from xd_view

		if(data.facebook_login_status){
			//check sh user
			facebook_access_token = data.facebook_access_token;
			facebook_user_id = data.facebook_user_id;
			sh_login_status = data.sh_login_status;
			console.log(facebook_user_id, facebook_access_token);
			jQuery.getJSON(base_url+'tab/json_facebook_user_check/'+facebook_user_id+'/'+page_id,
				function(response){ 
				if(response.user_id){
					view_as = response.role;
					user_name = response.user_name;
					user_image = response.user_image;
					sh_load_bar();
					if(!sh_login_status){
						sh_login();
					}
					sh_visit();
				} else {
					console.log(response.role);view_as = response.role;
					sh_load_bar();
				}
			});
		} else {
			view_as = 'guest';
			sh_load_bar();
		}
		if(sh_non_fan_homepage){
			XD.postMessage({
				sh_message:'is_user_liked_page',
				facebook_page_id:facebook_page_id
			}, base_url+'xd', document.getElementById('xd_sh').contentWindow);
		}

	// } else if(message === 'sh_page_campaign_user_check'){
	// 	if(!data.sh_page_user){
	// 		//register page
	// 	} else if(!data.sh_campaign_user){
	// 		//register campaign
	// 	}
	} else if(message === 'sh_user_liked_page'){
		if(data.liked){

		} else {
			//show like button
			//replace content
			jQuery('body>*').not(':has(#sh-bar)').remove();
			jQuery('body').append(sh_non_fan_homepage_content);
		}
	} else if(message === "logged in facebook"){ //login_button.php
		facebook_access_token = data.fb_access_token;
		sh_login();
	} else if(message === "logged in"){ //xd.js 
		if(view_as === 'guest' || is_user_register_to_page) {
			sh_signup();
		} else {
			sh_signup_page();
		} 
	} else if(message === "logged out"){ //xd.js
		jQuery.fancybox({
			href: base_url + 'tab/logout/'+page_id+'/'+app_install_id
		});
	} 
	// old flows // don't delete yet
	// if(message.data.sh_message === "loaded"){ //xd_view
	// 	XD.postMessage({
	// 		sh_message:'get_user_role',
	// 		sh_page_id:page_id,
	// 		facebook_page_id:facebook_page_id
	// 	}, base_url+'xd', document.getElementById('xd_sh').contentWindow);
	// } else if(message.data.sh_message === 'status'){ 
	// 	view_as = message.data.sh_status;
	// 	user_image = message.data.sh_user_image;
	// 	user_name = message.data.sh_user_name;
	// 	onLoad();
	// 	jQuery.get(base_url+'xd/get_bar_content/'+view_as+'/'+user_id+'/'+page_id+'/'+app_install_id, function(data){
	// 		sh_jq('div#sh-bar').html(data);
	// 	});
	// } else if(message.data.sh_message === "logged in facebook"){ //login_button.php
	// 	facebook_access_token = message.data.fb_access_token;
	// 	sh_login();
	// } else if(message.data.sh_message === "logged in"){ //xd.js 
	// 	if(view_as === 'guest' || is_user_register_to_page) {
	// 		sh_signup();
	// 	} else {
	// 		sh_signup_page();
	// 	} 
	// } else if(message.data.sh_message === "logged out"){ //xd.js
	// 	jQuery.fancybox({
	// 		href: base_url + 'tab/logout/'+page_id+'/'+app_install_id
	// 	});
	// } else if(message.data.sh_message === "facebook page like"){ //xd.js
	// 	if(message.data.liked){
	// 		// console.log('like');
	// 	} else {
	// 		// console.log('unlike');
	// 		//call xd/homepage/app_install_id [if app_install_id is defined]
	// 		if(app_install_id){
	// 			jQuery.getJSON(base_url+'xd/homepage/'+app_install_id, function(data){
	// 				if(typeof data.html !== 'undefined'){
	// 					jQuery('body>*').not(':has(#sh-bar)').remove();
	// 					jQuery('body').append(data.html);
	// 				}
	// 			});
	// 		}
	// 	}
	// } else if (message.data.sh_message === "autologin"){ //xd_view
	// 	onLoad();
	// 	jQuery.get(base_url+'xd/get_bar_content/'+view_as+'/'+user_id+'/'+page_id+'/'+app_install_id, function(data){
	// 		sh_jq('div#sh-bar').html(data);
	// 	});
	// 	facebook_access_token = message.data.fb_access_token;
	// 	sh_login();
	// }
}, sh_domain);


