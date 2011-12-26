<div class="facebook-connect">
	<script type="text/javascript">
		function fblogin() {
			FB.login(function(response) {
				if (response.authResponse) {
					window.location.replace('<?php echo $next; ?>');
				} else {
					//facebook not logged in
				}
			}, {scope:'<?php echo $facebook_default_scope ; ?>'});
		}
	</script>

	<a onclick="fblogin();" ><img src="<?php echo base_url(); ?>images/fb-login.jpg" alt=""></a>
</div>
