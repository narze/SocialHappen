<div>
	<h1><span>SocialHappen</span></h1>	
	<?php if(!issetor($facebook_user)) : ?>
		<script>
		function fblogin() {
					FB.login(function(response) {
						if (response.session) {
							FB.api('/me', function(response) {
								$.getJSON(base_url+"api/request_user_id?user_facebook_id=" + response.id , function(json){
									if(json.status != 'OK'){
										window.location.replace(base_url+"home/signup");
									} else {
										<?php if(issetor($next)): ?>
											window.location.replace('<? echo $next; ?>');
										<?php else : ?>
											window.location.replace(window.location.href+"?logged_in=true");
										<?php endif; ?>
									}
								});
							});
						} else {
							
						}
					}, {perms:'<? echo $facebook_default_scope ; ?>'});
				}
		</script>
		<ul>
			<li class="fb"><a onclick="fblogin();" ><img src="<?php echo base_url(); ?>images/fb-login.jpg" alt=""></a></li>
		</ul>
	<?php elseif(issetor($facebook_user)) : ?>
		<?php if(isset($user) && $user && isset($user_companies)) : ?>
	<div class="goto">
        <p><a href="#">Go to</a></p>
		 <div>
          <ul>
          </ul>
		  <?php if($user_can_create_company) : ?>
			<p><a class="bt-create_company" href="#"><span>Create Company</span></a></p>
		  <?php endif; ?>
        </div>
	</div>
	<ul>
		<li class="name">
			<img class="user-image" src="<?php echo imgsize(issetor($user['user_image']),'square');?>" alt="" />
			<?php echo issetor($user['user_first_name']).' '.issetor($user['user_last_name']); ?>
			<ul>
				<li><?php echo anchor("settings?s=account&id={$user['user_id']}",'&raquo Profile Setting');?></li>
				<li><?php echo anchor('logout','&raquo Logout');?></li>
			</ul>
		</li>
	</ul><?php else : ?>
		<ul>
		<li class="name">
			<img src="<?php echo imgsize("http://graph.facebook.com/{$facebook_user['id']}/picture",'square');?>" alt="" />
			<?php echo issetor($facebook_user['name']); ?>
			<ul>
				<li><?php echo anchor("home/signup",'&raquo Signup');?></li>
				<li><?php echo anchor('logout','&raquo Logout');?></li>
			</ul>
		</li>
	</ul>
	<?php endif; ?>
	<?php endif; ?>
</div>
