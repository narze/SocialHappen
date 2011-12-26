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
	  };
	 // Load the SDK Asynchronously
	  (function(d){
	     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
	     js = d.createElement('script'); js.id = id; js.async = true;
	     js.src = "//connect.facebook.net/en_US/all.js";
	     d.getElementsByTagName('head')[0].appendChild(js);
	   }(document));
	</script>