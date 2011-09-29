<div class="popup-fb-2col">
    <div>
      <div><img src="<?php echo base_url(); ?>images/slide-show260-190.jpg" alt="" /></div>
      <div>
        <div class="join">
          <h2>Join Social Happen Now!!</h2>
          <ol>
            <li>Discovery awesom campain</li>
            <li>Win leader board </li>
            <li>....</li>
          </ol>
			<div id="fb-root"></div>
			<script>
			  window.fbAsyncInit = function() {
				FB.init({appId: '<?php echo $facebook_app_id; ?>', status: true, cookie: true,
						 xfbml: true});
			  };
			  (function() {
				var e = document.createElement('script'); e.async = true;
				e.src = document.location.protocol +
				  '//connect.facebook.net/en_US/all.js';
				document.getElementById('fb-root').appendChild(e);
			  }());
			</script>
			<script type="text/javascript">
				function fblogin() {
					FB.login(function(response) {
						if (response.session) {
							$.fancybox({
								href: base_url+'tab/signup/'+page_id
							});
							$('form.signup-form').die('submit');
							$('form.signup-form').live('submit', function() {
								$(this).ajaxSubmit({target:'#signup-form'});
								return false;
							});
							
							$('a.bt-register-now').live('click', function(){
								$('form.signup-form').ajaxSubmit({target:'.popup-fb-2col', replaceTarget:true});
								return false;
							});
						} else {

						}
					}, {perms:'<? echo $facebook_default_scope ; ?>'});
				}
			</script>
	
          <p><a class="bt-join-social" onclick="fblogin();"><span>Join Social happen</span></a></p>
          <p><a class="bt-don-awesome"><span>I don't awesome stuff</span></a></p>
        </div>
        
      </div>
    </div>
  </div>