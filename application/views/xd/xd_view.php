<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script>
	var base_url = "<?php echo base_url(); ?>";
	<?php if(isset($vars)) :
		foreach($vars as $name => $value) :
			echo "var {$name} = '{$value}';\n";
		endforeach; 
	endif; ?>
</script>
<script src="<?php echo base_url().'assets/js/xd/xd.js'; ?>" type="text/javascript"></script>
<div id="fb-root"></div>
<script type="text/javascript">
	window.fbAsyncInit = function() {
		FB.init({
			appId  : '<?php echo $facebook_app_id; ?>',
			channelURL : '<?php echo $facebook_channel_url;?>',
			status : true,
			cookie : true,
			xfbml  : true,
			oauth : true
		});
		// send({sh_message:'loaded'});
		FB.getLoginStatus(function(response) {
		  var fb_login_status = false;
		  if (response.status === 'connected') {
		  	fb_login_status = true;
		  } else if (response.status === 'not_authorized') {
		    // the user is logged in to Facebook, 
		    //but not connected to the app
		  } else {
		    // the user isn't even logged in to Facebook.
		  }
		  send({
		  		sh_message:'facebook_login_status',
		  		facebook_login_status : fb_login_status,
		  		facebook_user_id : response.authResponse.userID,
				facebook_access_token : response.authResponse.accessToken,
				sh_login_status : <?php echo $sh_user_logged_in;?>
		  	});
		 });
		// fb_uid:response.authResponse.userID,
		// fb_access_token:response.authResponse.accessToken
	};	
	
	(function(d){
     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     d.getElementsByTagName('head')[0].appendChild(js);
   }(document));
</script>