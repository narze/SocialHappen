<div>
	<h1><span>SocialHappen</span></h1>	
	<?php if(isset($user) && $user) : ?>
	<div class="goto">
        <p><a href="#">Go to</a></p>
		 <div>
          <ul>
          </ul>
          <p><a class="bt-create_company" href="#"><span>Create Company</span></a></p>
        </div>
	</div>
	<ul>
		<li class="name">
			<img src="<?php echo imgsize(issetor($user['user_image']),'square');?>" alt="" />
			<?php echo issetor($user['user_first_name']).' '.issetor($user['user_last_name']); ?>
			<ul>
				<li><?php echo anchor("settings?s=account&id={$user['user_id']}",'&raquo Profile Setting');?></li>
				<li><?php echo anchor('logout','&raquo Logout');?></li>
			</ul>
		</li>
	</ul>
	<?php else : ?>
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
	<ul>
		<?php if(!isset($facebook_user)) { ?>
		<li class="fb"><a href="#" onclick="fblogin();" ><img src="images/fb-login.jpg" alt=""></a></li>
		<?php } ?>
    </ul>
	<?php endif; ?>
</div>
