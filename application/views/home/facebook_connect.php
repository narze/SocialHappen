<div class="popup_fb-connect">
	<p>Please login to facebook to continue</p>
	<div id="fb-root"></div>
	<script type="text/javascript">
		function fblogin() {
			FB.login(function(response) {
				if (response.authResponse) {
					FB.api('/me', function(response) {
						$.getJSON(base_url+"api/request_user_id?user_facebook_id=" + response.id , function(json){
							if(json.status != 'OK'){
								window.location.replace(base_url+"home/signup?package_id=<?php echo $this->input->get('package_id'); ?>&payment=true");
							} else { <?php 
								if($next) { ?> window.location.replace('<?php echo $next; ?>'); <?php }
								else { ?> window.location.replace(base_url+"home/login"); <?php }?>
							}
						});
					});
				} else {
					
				}
			}, {scope:'<?php echo $facebook_default_scope ; ?>'});
		}
	</script>

	<a onclick="fblogin();" ><img src="<?php echo base_url(); ?>images/fb-login.jpg" alt=""></a>
</div>
