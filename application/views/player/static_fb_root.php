<div id="fb-root"></div>
<script>
  	window.fbAsyncInit = function() {
		FB.init({appId: '<?php echo $facebook_app_id; ?>', 
			channelURL: '<?php echo $facebook_channel_url;?>', 
			status: true, 
			cookie: true,
			xfbml: true,
	 		oauth: true
		});
		FB.getLoginStatus(function(response) {
			if (response.status === 'connected' && typeof fbcallback === 'function') {
				fbcallback(response);
			}
		  	window.fblogin = function () {
					FB.login(function(response) {
						if (response.status === 'connected') {
							window.location = window.location.href;
						}
					}, {scope:'<?php echo $facebook_app_scope;?>'});
				};
		  // 	window.fblogin = function () {
				// 	FB.ui({
				// 		method: 'oauth',
				// 		client_id: '<?php echo $facebook_app_id; ?>',
				// 		redirect_uri: window.location.href, //have problem with X-frame
				// 		display: 'iframe'
				// 	},
				// 	function(response) {
				// 		if (response.status === 'connected') {
				// 			window.location = window.location.href;
				// 		}
				// 	}, {scope:'<?php echo $facebook_app_scope;?>'});
				// };
		});
  	};

  	(function(d){
    	var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
    	js = d.createElement('script'); js.id = id; js.async = true;
    	js.src = "//connect.facebook.net/en_US/all.js";
    	d.getElementsByTagName('head')[0].appendChild(js);
  	}(document));
</script>