<html>
<body>

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

				FB.Event.subscribe('edge.create',
				    function(response) {
				        window.location.href='callback://sh';
				    }
				);
		  };
		 // Load the SDK Asynchronously
		  (function(d){
		     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
		     js = d.createElement('script'); js.id = id; js.async = true;
		     js.src = "//connect.facebook.net/en_US/all.js";
		     d.getElementsByTagName('head')[0].appendChild(js);
		   }(document));
		</script>

<fb:like href="<?php echo $url ?>" show_faces="true" font="verdana" ref="pwfacebooktest" ></fb:like>

</body>
</html>