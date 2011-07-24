<!-- old

<div id="profile">
	<div id="image">
	</div>
	<div id="name">
	</div>
	<div id="points">
	<?php if($is_admin) :?>
	<a href="#">Dashboard</a>
	<?php endif; ?>
	</div>
	<?php if(!$is_liked) :?>
	<div>
	You haven't like this page yet.
	</div>
	<?php endif; ?>
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
</div>
<div id="installed-apps">
	<h3>Applications in this page</h3>
	<?php if(isset($installed_apps)) :
		foreach($installed_apps as $installed_app): ?>
		<div class="app">
			<div class="image">
				<img class="image" src="<?php echo imgsize($installed_app['app_image'],'square');?>" />
			</div>
			<div class="app-name">
				<?php echo $installed_app['app_name'];?>
			</div>
		</div>
	<?php endforeach; 
	endif;?>
</div>
-->

<!-- new -->

<div class="wrapper-content">
    <div class="account-data">
      <div class="pic"><img src="<?php echo imgsize($page['page_image'],'normal');?>" alt="" /><span></span></div>
      <div class="data">
        <h1><?php echo $page['page_name'];?></h1>
        <p><?php echo $page['page_detail'];?></p>
        <ul>
          <li class="fav"><a href="#">Add Favorite</a></li>
          <li class="member"><a href="#"><?php echo issetor($page_user_count,'-');?></a></li>
          <li class="badges"><a href="#"><?php echo issetor($page_badge_count,'-');?></a></li>
          <li class="point"><a href="#"><?php echo issetor($page_point_count,'-');?></a></li>
          <li class="setting"><a href="#"><span>setting</span></a></li>
        </ul>
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
      <div class="list_app-camp">
        <ul>
			
          
         <?php if(isset($campaigns)) :
		foreach($campaigns as $campaign): ?>
		<li>
            <div>
              <p class="pic"><img src="<?php echo imgsize($campaign['campaign_image'],'normal');?>" alt="" /></p>
              <h2><?php echo $campaign['campaign_name'];?></h2>
              <p><?php echo $campaign['campaign_detail'];?></p>
              <p class="link"><a href="#">read more</a></p>
            </div>
            <div>
				<h2>Remaining Time</h2>
				<div style="display: none;" class="campaign-end-time"><?php echo $campaign['campaign_end_timestamp'];?></div>
				<?php if($is_admin) :?>
				
				<?php else :?>
					<p><a class="bt-join" href="#"><span>Join</span></a></p>
				<?php endif; ?>   
            </div>
          </li>
		<?php endforeach; 
		endif;?>
	  
		<?php if(isset($apps)) :
		foreach($apps as $app): ?>
		<li>
            <div>
              <p class="pic"><img src="<?php echo imgsize($app['app_image'],'normal');?>" alt="" /></p>
              <h2><?php echo $app['app_name'];?></h2>
              <p><?php echo $app['app_description'];?></p>
              <p class="link"><a href="#">read more</a></p>
            </div>
            <?php if($is_admin) :?>
			<div>
				<p>Today : <?php echo '[]';?></p>
				<p>All : <?php echo '[]';?></p>
			</div>
			<?php endif; ?>   
           </li>
		<?php endforeach; 
		endif;?>
		 
        </ul>
      </div>
      <div class="strip">
        <ul>
          <li><a href="#"></a></li>
          <li><a class="current" href="#"></a></li>
          <li><a href="#"></a></li>
          <li><a href="#"></a></li>
        </ul>
      </div>
    </div>
    <div>
      <div class="tab-head">
        <h2>Recent Activity</h2>
        <div>
          <p>Display:</p>
          <ul>
            <li><a class="active" href="#">All</a></li>
            <li><a href="#">Applicaton</a></li>
            <li><a href="#">Campaign</a></li>
            <li><a href="#">Me</a></li>
          </ul>
        </div>
      </div>
      <div class="list_resent-activity"></div>
      <div class="strip">
        <ul>
          <li><a href="#"></a></li>
          <li><a class="current" href="#"></a></li>
          <li><a href="#"></a></li>
          <li><a href="#"></a></li>
        </ul>
      </div>
    </div>
  </div>