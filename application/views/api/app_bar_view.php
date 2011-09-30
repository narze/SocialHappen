<link rel="stylesheet" type="text/css"  href="<?php echo base_url().'assets/css/common/fancybox/jquery.fancybox-1.3.4.css'; ?>" />
<script>
	var base_url = "<?php echo base_url(); ?>";
	<?php if(isset($vars)) :
		foreach($vars as $name => $value) :
			echo "var {$name} = '{$value}';\n";
		endforeach; 
	endif; ?>
	if(typeof jQuery == 'undefined')
	{
		var file1 = document.createElement('script');
		file1.setAttribute("type","text/javascript");
		file1.setAttribute("src", 'http://code.jquery.com/jquery-latest.min.js');
	}
	if(typeof jQuery.fancybox == 'undefined')
	{
		var file1 = document.createElement('script');
		file1.setAttribute("type","text/javascript");
		file1.setAttribute("src", base_url + 'assets/js/common/fancybox/jquery.fancybox-1.3.4.pack.js');
	}
</script>
<script src="<?php echo base_url().'assets/js/api/bar.js'; ?>" type="text/javascript"></script>
<script src="<?php echo base_url().'assets/js/common/jquery.form.js'; ?>" type="text/javascript"></script>
<script>
	function shregister(){
		$.fancybox({
			href: '<?php echo base_url().'tab/signup/'.$page_id;?>'
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
	}
</script>
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
          <?php if($app_install_id) { ?>
		  <li><a href="<?php echo base_url().'app/config/'.$app_install_id?>" class="platform-link">App Settings</a></li>
		  <?php } else { ?>
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