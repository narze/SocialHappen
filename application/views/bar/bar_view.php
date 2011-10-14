<div>
	<h1><a href="<?php echo base_url();?>"><span>SocialHappen</span></a></h1>	
	<?php if(!issetor($facebook_user)) : ?>
		<script>
		function fblogin() {
					FB.login(function(response) {
						if (response.session) {
							FB.api('/me', function(response) {
								$.getJSON(base_url+"api/request_login?user_facebook_id=" + response.id , function(json){
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
		<?php if(isset($user) && $user) : ?>
			<div class="goto toggle">
				<p><a href="#">Go to</a></p>
				<ul>
					<?php //if(isset($user_companies) && $user_companies) {  } ?>
					<li>
						<img class="company-image" src="" alt="">
						<h2><a href="#"></a></h2>
						<p class="pagename">&raquo; <a href="#"></a></p>
						<p class="no-page">No page yet<br /><a href="#">+ add new page</a></p>
					</li>
					<?php if($user_can_create_company) { ?><li class="create-company"><a class="bt-create_company"><span>Create Company</span></a></li><?php } ?>
				</ul>
			</div>
			<ul class="menu">
				<li class="name toggle">
					<img class="user-image" src="<?php echo imgsize(issetor($user['user_image']),'square');?>" alt="" />
					<div class="arrow"></div>
					<?php echo issetor($user['user_first_name']).' '.issetor($user['user_last_name']); ?>
					<ul>
						<li><?php echo anchor("settings?s=account&id={$user['user_id']}",'&raquo Profile Setting');?></li>
						<li><?php echo anchor('logout','&raquo Logout');?></li>
					</ul>
				</li>
				<li class="notification toggle">
				<a class="amount"><?php if( isset($notification_amount) && $notification_amount > 0 ) { ?><span><?php echo $notification_amount;?></span> <?php } ?></a>
				<ul class="notification_list_bar">
					<li class="separator">
						<a>
							<p class="message"></p>
							<p class="time"></p>
						</a>
					</li>
					<li class="no-notification"><p>No notification.</p></li>
					<li class="separator last-child"><a class="a-notification" href="<?php echo $all_notification_link; ?>" >See all Notifications</a></li>
				</ul>
			</li>
			</ul>
		<?php else : ?>
			<ul>
				<li class="name toggle">
					<img class="user-image" src="<?php echo imgsize("https://graph.facebook.com/{$facebook_user['id']}/picture",'square');?>" alt="" />
					<?php echo issetor($facebook_user['name']); ?>
					<ul>
						<li><?php echo anchor("home/login",'&raquo Login');?></li>
						<li><?php echo anchor("home/signup",'&raquo Signup');?></li>
					</ul>
				</li>
			</ul>
		<?php endif; ?>
	<?php endif; ?>
</div>
<script src="<?php echo $node_base_url;?>socket.io/socket.io.js"></script>
<script>
	(function($){
	var session = 'SESSIONNAJA';
	var socket = io.connect('<?php echo $node_base_url;?>');

	socket.on('connect', function(){
		console.log('send subscribe');
		socket.emit('subscribe', user_id, session);
	});

	socket.on('subscribeResult', function (data) {
		console.log('got subscribe result: ' + JSON.stringify(data));
	});

	socket.on('newNotificationAmount', function (notification_amount) {
		console.log('notification_amount: ' + notification_amount);
		if(notification_amount > 0){
			$('div.header ul.menu li.notification a.amount').html('').append('<span>').children('span').html(notification_amount);
		}else{
			$('div.header ul.menu li.notification a.amount').append('<span>').children('span').remove();
		}
	});
	
	socket.on('newNotificationMessage', function (notification_message) {
		console.log('notification_message: ' + JSON.stringify(notification_message));
	});
	})(jQuery);
</script>
