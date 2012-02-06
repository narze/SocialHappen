<div class="name toggle">
	<?php if(in_array($view_as, array('admin','user'))) : ?>
		<div>
			<p class="pic">
				<img src="<?php echo $current_menu['icon_url'];?>" alt="<?php echo $current_menu['name'];?>" />
				<?php if(!$app_mode) { ?><span></span><?php } ?>
			</p>
			<p><?php echo $current_menu['name'];?></p>
			<?php if($menu['left']) :?>
				<span class="dropdown"></span>
			<?php endif; ?>
		</div>
		<?php if($menu['left']) :?>
		<ul><?php $last = count($menu['left']) - 1;
			foreach($menu['left'] as $key=>$item){
				$class = ($key == $last) ? ' class="last-child" ' : '';
				echo '<li'. $class .'><img src="'.$item['icon_url'].'" /><a target="'. issetor($item['target'], '_self') .'" href="'.$item['location'].'">'.$item['title'].'</a></li>';
			} ?>
		</ul>
		<?php endif; ?>
	<?php endif; ?>
</div>

<ul class="menu">
	<?php if($view_as == 'guest') : ?>
		<li class="guest last-child">
			<a onclick="sh_guest();">Sign up SocialHappen</a>
		</li>
	<?php endif; ?>
	<?php if(in_array($view_as, array('admin','user'))) : ?>
		<!-- <li class="like"><a><span>like</span></a></li> -->
		<li class="no-notification" style="display: none;"><p>No notification.</p></li>
		<li class="notification toggle">
			<a class="amount"><?php if( isset($notification_amount) && $notification_amount > 0 ) { ?><span><?php echo $notification_amount;?></span> <?php } ?></a>
			<ul class="notification_list_bar" style="display: none;">
				
				<li class="separator last-child"><a class="a-notification" href="<?php echo $all_notification_link; ?>" <?php echo $app_mode ? 'target="_top"' : ''; ?>>See all Notifications</a></li>
			</ul>
		</li>
		<li class="profile toggle<?php echo $view_as!='admin' ? ' last-child' : ''; ?>">
			<div>
				<p class="user-pic">
					<img src="<?php echo $user['user_image']; ?>" alt="<?php echo $user['user_first_name']. ' '. $user['user_last_name'];?>" />
					<span></span>
				</p>
				<p class="user-name"><?php echo $user['user_first_name']. ' '. $user['user_last_name'];?></p>
				<p class="user-point"><?php echo number_format($page_score);?></p>
			</div>
			<ul>
				<li class="user-info">
					<div>
						<img src="<?php echo $user['user_image'];?>" alt="<?php echo $user['user_first_name']. ' '. $user['user_last_name'];?>" />
						<p><?php echo $user['user_first_name']. ' '. $user['user_last_name'];?></p>
					</div>
				</li>
				<li><a class="a-profile">View my profile</a></li>
				<li><a class="a-account">Account Settings</a></li>
				<li class="last-child"><a class="a-logout">Sign out</a></li>
			</ul>
		</li>
	<?php endif; ?>
  
	<?php if($view_as == 'admin') : ?>
		<li class="setting toggle last-child">
			<div>Settings</div>
			<ul>
				
				<?php if($app_install_id) { ?>
					<li><a class="app-config" href="<?php echo base_url().'app/config/'.$app_install_id?>">Config <?php echo $current_menu['name']; ?></a></li>
					<li><a href="<?php echo base_url().'settings/page_apps/app/'.$page_id.'/'.$app_install_id?>" target="_top" >App Settings</a></li>
					<li class="separator"><a href="<?php echo base_url()."settings/page/".$page_id;?>" target="_top" >Page Settings</a></li>
					<li class="separator last-child"><a href="<?php echo base_url()."page/".$page_id;?>" id="a-dashboard" target="_top">Go to Dashboard</a></li>
				<?php } else { ?>
					<li><a class="a-dashboard">View as Admin</a></li>
					<li><a class="a-dashboard view-as-user">View as Member</a></li>
					<li><a class="a-dashboard view-as-guest">View as Guest</a></li>
					<li><a href="<?php echo base_url()."settings/page/".$page_id;?>" target="_top" >Page Settings</a></li>
					<li class="separator last-child"><a href="<?php echo base_url()."page/".$page_id;?>" id="a-dashboard" target="_top">Go to Dashboard</a></li>
				<?php } ?>
			</ul>
		</li>
	<?php endif; ?>
</ul>