<hr />
header bar
<div id="logo"><h2><a href="<?php echo base_url();?>home">SocialHappen</a></h2></div>
<div id="goto"><h2><a href="#">Go to</a></h2><div id="goto-list"></div></div>
<div id="user">
	<h2><a href="#">
	<?php if(isset($user)) {
				echo $user['user_first_name'].' '.$user['user_last_name']; 
			} else {
				echo 'Login';
			}?></a></h2>
	<?php if(isset($user)) : ?>
	<div id="user-list">
		<div id="profile-setting"><?php echo anchor('path/to/profilesetting','Profile Setting');?></div>
		<div id="logout"><?php echo anchor('home/logout','Logout');?></div>
	</div>
	<?php endif; ?>
</div>
end of header bar
<hr />
