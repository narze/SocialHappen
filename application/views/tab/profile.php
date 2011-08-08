<div class="wrapper-content">
    <div class="account-data">
      <div class="pic"><img src="<?php echo imgsize($user['user_image'],'normal');?>" alt="" /><span></span></div>
      <div class="data">
        <h1><?php echo $user['user_first_name']." ".$user['user_last_name'];?></h1>
		<p>Level: </p>
      </div>
	 
    </div>
	 <div id="friends">
		<?php if(isset($friends)) :
			foreach($friends as $friend): ?>
			<div class="friend">
				<div class="image">
					<img class="image" src=" http://graph.facebook.com/<?php echo $friend['uid'];?>/picture" />
				</div>
				<div class="friend-detail">
					<div class="name"><?php echo $friend['name'];?></div>
				</div>
			</div>
		<?php endforeach; 
		endif;?>
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