<p>please login to facebook to continue</p>
	<div id="fb-root"></div>
	<script type="text/javascript">
	  window.fbAsyncInit = function() {
		FB.init({
		  appId  : '<?php echo $facebook_app_id; ?>',
		  status : true, // check login status
		  cookie : true, // enable cookies to allow the server to access the session
		  xfbml  : true  // parse XFBML
		});
	  };

	  (function() {
		var e = document.createElement('script');
		e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
		e.async = true;
		document.getElementById('fb-root').appendChild(e);
	  }());
	
		function fblogin() {
			FB.login(function(response) {
				if (response.session) {
					FB.api('/me', function(response) {
						$.getJSON(base_url+"api/request_user_id?user_facebook_id=" + response.id , function(json){
							if(json.status != 'OK'){
								window.location.replace(base_url+"home/signup?package_id=<?php echo $this->input->get('package_id'); ?>&payment=true");
							} else {
								window.location.replace(base_url+"home/package?package_id=<?php echo $this->input->get('package_id'); ?>&payment=true");
							}
						});
					});
				} else {
					
				}
			}, {perms:'<? echo $facebook_default_scope ; ?>'});
		}
	</script>
	
<a onclick="fblogin();" ><img src="<?php echo base_url(); ?>images/fb-login.jpg" alt=""></a>