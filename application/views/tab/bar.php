<!--<div id="bar">
	<ul>
		<li><a class="socialhappen" href="#">Socialhappen</a></li>
		<li><a class="badges" href="<?php echo base_url()."tab/badges/{$page_id}";?>">Badges</a></li>
		<li><a class="leaderboard" href="<?php echo base_url()."tab/leaderboard/{$page_id}";?>">Leaderboard</a></li>
		<li><a class="favorites" href="<?php echo base_url()."tab/favorites/{$user_id}";?>">Favorite pages</a></li>
		<li><a class="notifications" href="<?php echo base_url()."tab/notifications/{$user_id}";?>">Notifications</a></li>
		<li><a class="profile" href="<?php echo base_url()."tab/profile/{$user_id}";?>">&raquo; <b>View my Profile</b></a></li>
	</ul>
</div>-->

<div class="header">
    <div class="name">
      <div>
        <p class="pic"><img src="<?php echo imgsize($page['page_image'],'square');?>" alt="" /><span></span></p>
        <p><?php echo $page['page_name'];?></p>  
      </div>
      <ul>
        <li><a href="#">&raquo; <b>Menu01</b></a></li>
        <li><a href="#">&raquo; <b>Menu01</b></a></li>
        <li><a href="#">&raquo; <b>Menu01</b></a></li>
        <li><a href="#">&raquo; <b>Menu01</b></a></li>
      </ul>
    </div>
    <ul class="menu">
      <li class="like"><a href="#"><span>like</span></a></li>
      <li class="message"><a href="#"><span><?php echo issetor($messages);?></span></a></li>
      <li class="profile">
        <div>
          <p class="pic"><img src="<?php echo $user['user_image'];?>" alt="" /><span></span></p>
          <p><?php echo $user['user_name'];?></p>  
        </div>
        <ul>
          <li><a href="#">&raquo; <b>Go to Dashboard</b></a></li>
          <li><a class="profile" href="<?php echo base_url()."tab/profile/{$user_id}";?>">&raquo; <b>View my Profile</b></a></li>
          <li><a href="#">&raquo; <b>View as Admin</b></a></li>
          <li><a href="#">&raquo; <b>View as Member</b></a></li>
          <li><a href="#">&raquo; <b>View as Guest</b></a></li>
          <li><a href="#">&raquo; <b>Account Setting</b></a></li>
        </ul>
      </li>
      <li class="setting"><a href="#"><span>setting</span></a></li>
    </ul>
  </div>