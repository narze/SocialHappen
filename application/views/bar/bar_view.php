<div>
	<h1><a href="<?php echo base_url();?>"><span>SocialHappen</span></a></h1>	
	

	<?php if(isset($user) && $user) { ?>
		<?php if($user['user_is_player'] == 0) { ?>
		<div class="goto toggle">
			<p><a href="#">Go to</a></p>
			<ul>
				<li class="company">
					<img class="company-image" src="" alt="">
					<h2><a href="#"></a></h2>
					<p class="pagename">&raquo; <a href="#"></a></p>
					<p class="no-page">No page yet<br /><a href="#">+ add new page</a></p>
				</li>
				<?php if($user_can_create_company) { ?><li class="create-company"><a class="bt-create_company btn btn-primary"><span>Create Company</span></a></li><?php } ?>
			</ul>
		</div>
		<?php } ?>
		<ul class="menu">
			<li class="name toggle">
				<img class="user-image" src="<?php echo imgsize(issetor($user['user_image']),'square');?>" alt="" />
				<div class="arrow"></div>
				<?php echo issetor($user['user_first_name']).' '.issetor($user['user_last_name']); ?>
				<ul>
					<li><?php echo anchor("settings/account/{$user['user_id']}",'&raquo Profile Setting');?></li>
					<li><?php echo anchor('logout','&raquo Logout');?></li>
				</ul>
			</li>
			<li class="notification toggle">
				<a class="amount"><?php if( isset($notification_amount) && $notification_amount > 0 ) { ?><span><?php echo $notification_amount;?></span> <?php } ?></a>
				<ul class="notification_list_bar">
					<li class="no-notification"><p>No notification.</p></li>
					<li class="separator last-child"><a class="a-notification" href="<?php echo $all_notification_link; ?>" >See all Notifications</a></li>
				</ul>
			</li>
		</ul>
	<?php } else { ?>
		<ul>
			<?php if(issetor($facebook_user)) { ?>
			<li class="name toggle">
				<img class="user-image" src="<?php echo imgsize("https://graph.facebook.com/{$facebook_user['id']}/picture",'square');?>" alt="" />
				<?php echo issetor($facebook_user['name']); ?>
				<ul>
					<li>
						<a onclick="fblogin();" href="#" >&raquo; Login</a>
					</li>
					<li><?php echo anchor("home/signup",'&raquo Signup');?></li>
				</ul>
			</li>
			<?php } else { ?>
			<li class="fb">
				<?php $next = isset($_GET['next']) ? '?next='.urlencode($_GET['next']) : NULL; ?>
				<a href="home/login<?php echo $next; ?>" id="bar-login">&raquo; Login</a>
				<!-- <a onclick="fblogin();" ><img src="<?php //echo base_url(); ?>images/fb-login.jpg" alt=""></a> -->
			</li>
			<?php } ?>
		</ul>
	<?php } ?>
</div>
<?php if(issetor($facebook_user) && issetor($user)) { ?>
	<script src="<?php echo base_url().'assets/js/api/socket.io.min.js';?>"></script>
	<script>
		(function($){
			if(typeof io != 'undefined'){
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
			}
		})(jQuery);
	</script>
<?php } ?>
