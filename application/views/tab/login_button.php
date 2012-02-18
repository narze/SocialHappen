<div id="fb-root"></div>
<script type="text/javascript">

	
	var parent_url = decodeURIComponent(document.location.hash.replace(/^#/, ''));

	function send(msg) {
        XD.postMessage(msg, parent_url, parent);
        return false;
    }
	
	window.fbAsyncInit = function() {
		FB.init({
			appId  : '<?php echo $facebook_app_id; ?>',
            channelURL : '<?php echo $facebook_channel_url;?>',
			status : true,
			cookie : true,
			xfbml  : true,
			oauth : true
		});
        FB.getLoginStatus(function(response){
            window.fblogin = function () {
                FB.login(function(response) {
                    if (response.authResponse) {
                        send({sh_message:'logged in facebook',fb_uid:response.authResponse.userID,fb_access_token:response.authResponse.accessToken});
                    }
                }, {scope:'<?php echo $facebook_default_scope ; ?>'});
            };
        });
	};	
	
	(function(d){
     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     d.getElementsByTagName('head')[0].appendChild(js);
   }(document));
 
	
	
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
        }/*,
  
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
        }*/
    };
}();
</script>
<style type="text/css">
	*.bt-join-social {
		background: url(../assets/images/bg_fb/bg_button.png) no-repeat;
		cursor: pointer;
		border: none;
		color: transparent;
	}
	*.bt-join-social span {display: none;}
	*.bt-join-social {
		display: block;
		margin: 0 auto;
		width: 175px;
		height: 32px;
		background-position: 0 -502px;
	}
</style>
<p><a class="bt-join-social" onclick="fblogin();"><span>Join Social happen</span></a></p>