<div id="fb-root"></div>
<script type="text/javascript">
	window.fbAsyncInit = function() {
		FB.init({
			appId  : '<?php echo $facebook_app_id; ?>',
			status : true,
			cookie : true,
			xfbml  : true
			//oauth : true
		});
	};	
	
	(function() {
		var e = document.createElement('script'); e.async = true;
		e.src = document.location.protocol +
		  '//connect.facebook.net/en_US/all.js';
		document.getElementById('fb-root').appendChild(e);
	}());
 
	function fblogin() {
		FB.login(function(response) {
			if (response.session) {
				parent.sh_register(); //Call popup function
			}
		}, {perms:'<?php echo $facebook_default_scope ; ?>'});
	}
	
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