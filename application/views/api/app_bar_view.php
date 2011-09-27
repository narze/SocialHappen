<script>
	if(typeof jQuery == 'undefined')
	{
		var fileref = document.createElement('script');
		fileref.setAttribute("type","text/javascript");
		fileref.setAttribute("src", 'http://code.jquery.com/jquery-latest.min.js');
	}
</script>
<script src="<?php echo base_url().'assets/js/api/bar.js'; ?>" type="text/javascript"></script>
<div class="header">
    
	<div class="name toggle">
	<?php if(in_array($view_as, array('admin','user'))) : ?>
      <div>
        <p class="pic"><img src="<?php echo $current_menu['icon_url'];?>" alt="<?php echo $current_menu['name'];?>" /><span></span></p>
        <p><?php echo $current_menu['name'];?></p>
		<?php if($menu['left']) :?><div class="dropdown"><span></span></div><?php endif; ?>
      </div>
	  <?php if($menu['left']) :?>
		<ul><?php $last = count($menu['left']) - 1;
			foreach($menu['left'] as $key=>$item){
				$class = ($key == $last) ? ' class="last-child" ' : '';
				echo '<li'. $class .'><a target="'. issetor($item['target'], '_self') .'" href="'.$item['location'].'">'.$item['title'].'</a></li>';
			} ?>
		</ul>
	  <?php endif; ?>
	  <?php endif; ?>
    </div>
	
    <ul class="menu">

	  <?php if($view_as == 'guest') : ?>
	  <li class="guest">
		<a href="<?php echo $signup_link; ?>">Sign up SocialHappen</a>
	  </li>
	  <?php endif; ?>

	  <?php if(in_array($view_as, array('admin','user'))) : ?>
	  <!-- <li class="like"><a><span>like</span></a></li> -->
      <li class="message"><a><?php if( isset($messages) && count($messages)>0 ) { ?><span><?php echo count($messages);?></span> <?php } ?></a></li>
	  <li class="profile toggle">
        <div>
			<p class="pic">
				<img src="<?php echo $user['user_image']; ?>" alt="<?php echo $user['user_first_name']. ' '. $user['user_last_name'];?>" />
				<span></span>
			</p>
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
	  <li class="setting toggle">
		<div>Settings</div>
		<ul>
          <li><a href="<?php echo base_url()."page/".$page_id;?>" class="platform-link">Page Settings</a></li>
          <?php if($bar_type == 'app') { ?>
		  <li><a href="<?php echo base_url().'app/config/'.$app_install_id?>" class="platform-link">App Settings</a></li>
		  <?php } ?>
          <?php if($bar_type == 'platform') { ?>
		  <li class="separator"><a class="a-dashboard">View as Admin</a></li>
		  <li><a class="a-dashboard view-as-user">View as Member</a></li>
		  <li><a class="a-dashboard view-as-guest">View as Guest</a></li>
		  <?php } ?>
		  <li class="separator platform-link last-child"><a href="<?php echo base_url(); ?>" id="a-dashboard">GO TO DASHBOARD</a></li>
        </ul>
      </li>
	  <?php endif; ?>
	  
    </ul>
  </div>