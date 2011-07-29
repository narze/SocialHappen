<div class="wrapper-details">
	<h1>Home</h1>
		<p>
		<div id="fb-root"></div>
		<script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>
		<script type="text/javascript">
			FB.init({appId: '<?php echo $facebook_app_id; ?>', status: true, cookie: true, xfbml: true});
		
			function fblogin() {
				FB.login(function(response) {
					if (response.session) {
						window.location.replace('<? echo issetor($next,base_url()); ?>');
					} else {
						
					}
				}, {perms:'<? echo $facebook_default_scope ; ?>'});
			}
					
		</script>
		<button type="button" onclick="fblogin();">Facebook Login</button>
	</p>
</div>