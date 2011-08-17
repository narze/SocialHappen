<div class="wrapper-content">
    <div class="account-data">
      <div class="pic"><img src="<?php echo imgsize($user['user_image'],'normal');?>" alt="" /><span></span></div>
      <div class="userview">
        <h1><?php echo $user['user_first_name']." ".$user['user_last_name'];?></h1>
        <p>Level: 3</p>
        <ul>
          <li class="fav">20,000</li>
          <li class="point">120</li>
        </ul>
      </div>
      <div class="friend">
        <ul>
		<?php if(isset($friends)) :
			foreach($friends as $friend): ?>
          <li><a href="#"><img class="image" src=" http://graph.facebook.com/<?php echo $friend['uid'];?>/picture" title="<?php echo $friend['name'];?>" /></a></li>
		  <?php endforeach; 
		endif;?>
		  <li><a href="#"><img src="<?php echo base_url(); ?>assets/images/thumb_30-30-2.jpg" title="Tiffany" /></a></li>
          <li><a href="#"><img src="<?php echo base_url(); ?>assets/images/thumb_30-30-1.jpg" title="Taeyeon" /></a></li>
          <li><a href="#"><img src="<?php echo base_url(); ?>assets/images/thumb_30-30-2.jpg" title="Tiffany" /></a></li>
          <li><a href="#"><img src="<?php echo base_url(); ?>assets/images/thumb_30-30-1.jpg" title="Taeyeon" /></a></li>
        </ul>
        <p><a class="link-friendsjoin" href="#">1,220 friends joined this page</a></p>
        <p><a class="link-invite_friend" href="#">invite friends</a></p>
      </div>
    </div>
	
      <div>
      <div class="tab-head">
        <h2>Application and Campaign</h2>
        <div>
          <p>Display:</p>
          <ul>
            <li><a class="active a-app-campaign">All</a></li>
            <li><a class="a-app">Applicaton</a></li>
            <li><a class="a-campaign">Campaign</a></li>
          </ul>
        </div>
      </div>
      <div class="list_app-camp"></div>
      <div class="pagination-app-campaign strip"></div>
    </div>
    <div>
      <div class="tab-head">
        <h2>Recent Activity</h2>
        <div>
          <p>Display:</p>
          <ul>
            <li><a class="active a-activity-app-campaign">All</a></li>
            <li><a class="a-activity-app">Applicaton</a></li>
            <li><a class="a-activity-campaign">Campaign</a></li>
          </ul>
        </div>
      </div>
      <div class="list_resent-activity"></div>
      <div class="pagination-activity strip"></div>
    </div>
  </div>