<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="description" content="SocialHappen" />
</head>
<body>
	<div id="fb-root"></div>
	<script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>
		<script type="text/javascript">
			FB.init({
				appId: '<?php echo $facebook_app_id; ?>', 
				channelURL : '<?php echo $facebook_channel_url;?>',
				status: true, 
				cookie: true, 
				xfbml: true, 
				oauth: true
			});
			FB.logout(function(){
				window.location.replace('<?php echo $redirect_url ? $redirect_url : base_url(); ?>');
			});
		</script>
</body>
</html>