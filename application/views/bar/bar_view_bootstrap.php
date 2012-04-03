<div class="navbar-inner">
	<div class="container">

		<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</a>

		<a class="brand" href="<?php echo base_url();?>">SocialHappen</a>

		<div class="nav-collapse">
			<ul class="nav">
				<!--
				<li class="active"><a href="#">Home</a></li>
				<li><a href="#">About</a></li>
				<li><a href="#">FAQ</a></li>
				-->
				<?php if(isset($user) && $user) { ?>
				<li class="divider-vertical"></li>
				<li class="dropdown">
					<a href="#" class="goto dropdown-toggle" data-toggle="dropdown">Go to <b class="caret"></b></a>
					<ul class="dropdown-menu mega-dropdown-menu companies">
						<li class="company">
							<img class="company-image" src="" alt="">
							<p class="company-name"><a href="#">Company name</a></p>
							<p class="page-name">&raquo; <a href="#">Page name</a></p>
							<p class="no-page">No page yet<br /><a href="#">+add new page</a></p>
						</li>
						<?php if($user_can_create_company) { ?>
						<li class="create-company">
							<button type="button" class="bt-create_company btn btn-primary">Create Company</button>
						</li>
						<?php } ?>
					</ul>
				</li>
				<li class="divider-vertical"></li>
				<?php } ?>
			</ul>

			<ul class="nav pull-right">
				<li class="divider-vertical"></li><?php 

				if(isset($user) && $user) 
				{ ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<img class="user-image" src="<?php echo $user['user_image'] ? imgsize(issetor($user['user_image']),'square') : base_url('assets/images/default/user.png');?>" alt="" />
							<?php echo $user['user_first_name'].' '.$user['user_last_name']; ?>
							<b class="caret"></b>
						</a>
						<ul class="dropdown-menu mega-dropdown-menu user">
							<li><?php echo anchor("settings/account/{$user['user_id']}",'&raquo Profile Setting');?></li>
							<li><?php echo anchor('logout','&raquo Logout');?></li>
						</ul>
					</li>

					<li class="divider-vertical"></li>

					<li class="dropdown notification">
						<a href="#" class="dropdown-toggle amount" data-toggle="dropdown">
							<?php if( isset($notification_amount) && $notification_amount > 0 ) { ?><span><?php echo $notification_amount;?></span> <?php } ?>
						</a>
						<ul class="dropdown-menu mega-dropdown-menu notification_list_bar">
							<li class="no-notification"><p>No notification.</p></li>
							<li class="divider"></li>
							<li class="all-notification">
								<a class="a-notification" href="<?php echo $all_notification_link; ?>" >See all Notifications</a>
							</li>
						</ul>
					</li><?php 
				} 
				else 
				{
					if(isset($facebook_user) && $facebook_user) 
					{ ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img class="user-image" src="<?php echo imgsize("https://graph.facebook.com/{$facebook_user['id']}/picture",'square');?>" alt="" />
								<?php echo issetor($facebook_user['name']); ?>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu mega-dropdown-menu user">
								<li><a onclick="shlogin();" href="<?php echo base_url('player/login'); ?>">&raquo; Login</a></li>
								<li><?php echo anchor("home/signup",'&raquo Signup');?></li>
							</ul>
						</li><?php 
					} 
					else 
					{ ?>
						<li>
							<a onclick="shlogin();" href="<?php echo base_url('player/login'); ?>">&raquo; Login</a>
							<!-- <a onclick="fblogin();" ><img src="<?php //echo base_url(); ?>images/fb-login.jpg" alt=""></a> -->
						</li><?php 
					}
				} ?>
				<li class="divider-vertical"></li>
			</ul>
        </div><!-- /.nav-collapse -->
	</div><!-- /.container -->
</div><!-- /.navbar-inner -->

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
