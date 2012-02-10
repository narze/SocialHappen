var parent_url = decodeURIComponent(document.location.hash.replace(/^#/, ''));
var parent_origin = parent_url.replace( /([^:]+:\/\/[^\/]+).*/, '$1');

function send(msg) {
	XD.postMessage(msg, parent_url, parent);
	return false;
}

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
                    attached_callback = function(e) {
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
	
XD.receiveMessage(function(message){ // Receives data from parent
    if(message.data.sh_message == 'login'){ //bar.js
        // alert('login');
        jQuery('<iframe />', {
            name: 'sh_frame_login',
            id:   'sh_frame_login',
            src: base_url+'xd/login'
        }).appendTo('body');
        send({sh_message:'logged in'});
    } else if(message.data.sh_message == 'logout'){ //bar.js
        // alert('logout');
        jQuery('<iframe />', {
            name: 'sh_frame_logout',
            id:   'sh_frame_logout',
            src: base_url+'xd/logout'
        }).appendTo('body');
        send({sh_message:'logged out'});
    } else if(message.data.sh_message === 'is_user_liked_page'){ //bar.js
        doesUserLikeFacebookPage(message.data.facebook_page_id);
        
    } else if(message.data.sh_message === 'visit'){ //bar.js
        jQuery.getJSON(base_url+'xd/visit/'+message.data.sh_page_id+'/'
            +message.data.sh_app_install_id+'/'+message.data.sh_app_id,function(json){
            console.log(json);
        });
    }
return;
    if(message.data.sh_message == 'login'){ //bar.js
		// alert('login');
		jQuery('<iframe />', {
			name: 'sh_frame_login',
			id:   'sh_frame_login',
			src: base_url+'xd/login'
		}).appendTo('body');
        send({sh_message:'logged in'});
	} else if(message.data.sh_message == 'logout'){ //bar.js
		// alert('logout');
		jQuery('<iframe />', {
			name: 'sh_frame_logout',
			id:   'sh_frame_logout',
			src: base_url+'xd/logout'
		}).appendTo('body');
        send({sh_message:'logged out'});
	} else if(message.data.sh_message === 'get_user_role'){ //bar.js
        doesUserLikeFacebookPage(message.data.facebook_page_id);
		jQuery.getJSON(base_url+'xd/get_user/'+message.data.sh_page_id,function(json){
			if(typeof json.user_id !== 'undefined'){
				send({sh_message:'status',
                    sh_status:json.user_role,
                    sh_user_image:json.user_image,
                    sh_user_name:json.user_first_name
                });
			}
		});
	} else if(message.data.sh_message === 'homepage'){ //bar.js
         
    }
}, parent_origin);

function doesUserLikeFacebookPage(facebook_page_id){
    jQuery.getJSON(base_url+'xd/is_user_liked_page/'+facebook_page_id,function(response){
        if(!response.error){
            send({sh_message:'sh_user_liked_page', liked: response.data.length != 0});
        } else {
            // console.log(response);
        }
    });
}