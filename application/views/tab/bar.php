<div class="header">
    <div class="name">
      <div>
        <p class="pic"><img src="<?php echo imgsize($page['page_image'],'square');?>" alt="" /><span></span></p>
        <p><?php echo $page['page_name'];?></p>  
      </div>
	<?php if($page_apps) :?>
		<ul>
			<?php
				foreach($page_apps as $item){
					echo '<li><a href="'.$item['location'].'">'.$item['title'].'</a></li>';
				}
			?>
		</ul>
	<?php endif; ?>
    </div>
    <ul class="menu">
      <!--
	  <li class="like"><a><span>like</span></a></li>
      <li class="message"><a><?php if( isset($messages) && count($messages)>0 ) { ?><span><?php echo count($messages);?></span> <?php } ?></a></li>
      -->
	  <li class="profile">
        <div>
          <p class="pic"><img src="<?php echo $user['user_image'];?>" alt="" /><span></span></p>
          <p><?php echo "{$user['user_first_name']} {$user['user_last_name']}";?></p>  
        </div>
        <ul>
          <li><a id="a-dashboard" class="a-dashboard"><b>Go to Dashboard</b></a></li>
          <li><a class="a-profile"><b>View my Profile</b></a></li>
		  <?php if($is_admin) :?>
			  <li><a class="a-dashboard"><b>View as Admin</b></a></li>
			  <li><a class="a-dashboard view-as-user"><b>View as Member</b></a></li>
			  <li><a class="a-dashboard view-as-guest"><b>View as Guest</b></a></li>
		  <?php endif; ?>
          <li><a class="a-account"><b>Account Setting</b></a></li>
        </ul>
      </li>
    </ul>
  </div>