<hr />
header bar
<div id="logo"><h2><a href="<?php echo base_url();?>home">SocialHappen</a></h2></div>
<div id="goto"><h2><a href="#">Go to</a></h2><div id="goto-list"></div></div>
<div id="user">
	<h2><a href="#"><?php if(isset($user['user_first_name']) && isset($user['user_last_name'])) echo $user['user_first_name'].' '.$user['user_last_name'];?></a></h2>
	<div id="user-list">
		<div id="profile-setting"><?php echo anchor('path/to/profilesetting','Profile Setting');?></div>
		<div id="logout"><?php echo anchor('home/logout','Logout');?></div>
	</div>
</div>
end of header bar
<hr />
