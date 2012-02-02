<div class="wrapper-content">
    <div class="account-data">
      <div class="pic"><img src="<?php echo $user['user_image'].'?type=normal';?>" /><span></span></div>
      <div class="data">
        <h1><?php echo $user['user_first_name']." ".$user['user_last_name'];?></h1>
        <ul class="counter">
          <li class="member" alt="Member"><a><?php echo issetor($page_user_count,'-');?></a></li>
          <li class="activities" alt="Activities"><a><?php echo issetor($page_activities_count,'-');?></a></li>
          <li class="applications" alt="Applications"><a><?php echo issetor($page_apps_count,'-');?></a></li>
          <li class="campaigns" alt="Campaigns"><a><?php echo issetor($page_campaigns_count,'-');?></a></li>
        </ul>
      </div>

<?php /*
      <div class="friend">
        <ul><?php
			if(isset($friends)) :
			foreach($friends as $friend): ?>
				<li><a><img class="image" src="<?php echo  $friend['image']; ?>" title="<?php echo $friend['name'];?>" /></a></li><?php 
			endforeach; 
			endif;?>
        </ul>
        <p><a class="link-friendsjoin">1,220 friends joined this page</a></p>
        <p><a class="link-invite_friend">invite friends</a></p>
      </div>
*/ ?>

      

    </div><!-- end account-data-->

    <div class="main-memu tab-white mb15">
      <div class="tab active">Dashboard</div>
      <div class="tab">Badges</div>
      <div class="tab">Activities</div>
    </div>
	
    <div>
      <div class="tab-head slim campaign">
        <p class="tab-name">My Campaign</p>
        <span class="fr">
            <a class="tab active" data-filter="">All</a>
            <a class="tab" data-filter="active">Active</a>
            <a class="tab" data-filter="expired">Expired</a>
        </span>
        <p class="fr p10">Display:</p>
      </div>
      <div class="list_app-camp"></div>
      <div class="pagination-app-campaign strip"></div>
    </div>

    <div>
      <div class="tab-head slim applications">
        <p class="tab-name">My Applications</p>
        <a class="view-all-apps fr underline">View all</a>
      </div>
      <div class="app-icon-list<?php echo $user_apps ? '' : ' no-app'; ?>"><?php
          if($user_apps)
          {
            foreach($user_apps as $app)
            { ?> <div class="app-container">
                <a class="app-icon" href="<?php echo base_url().'app/'.$app['app_id']; ?>" title="<?php echo $app['app_name']; ?>" ><img class="app-image" width="64" height="64" src="<?php echo $app['app_image']; ?>" onerror="failsafeImg(this)" /></a>
                <a class="app-name" href="<?php echo base_url().'app/'.$app['app_id']; ?>" title="<?php echo $app['app_name']; ?>" ><?php echo $app['app_name']; ?></a>
              </div><?php
            }
          }
          else
          { ?>
            <li class="app-container">No application.</li><?php
          } ?>
      </div>
    </div>

    <div>
      <div class="tab-head slim reward">
        <p class="tab-name">Wishlist</p>
        <span class="fr">
            <a class="tab active" data-filter="">All</a>
            <a class="tab" data-filter="active">Active</a>
            <a class="tab" data-filter="expired">Expired</a>
        </span>
        <p class="fr p10">Display:</p>
      </div>

      <div class="list_reward p25">

      <?php if($wishlist_items)
      {
        foreach ($wishlist_items as $reward_item) 
        { ?>      
          <div class="reward-item" data-item-id="<?php echo $reward_item['_id'];?>">
            <div class="section first">
              <div class="item-image" style="background-image:url(<?php echo $reward_item['image'] ? $reward_item['image'] : base_url().'assets/images/default/reward.png'; ?>);">
                <div class="remaining-time abs-b bold tc-blue1">Remaining Time <span class="end-time-countdown bold tc-grey5 fr"><?php echo $reward_item['end_date']; ?></span></div>
              </div>
              <ul class="item-info">
                <li class="box">
                  <span class="tc-green6 bold">User who get this reward : </span><?php 
                  if (count($reward_item['user_list'])>0) {
                    foreach ($reward_item['user_list'] as $user_id) { ?>
                      <a href="#<?php echo $user_id; ?>" title="<?php echo $user_list[$user_id]['user_first_name'].' '.$user_list[$user_id]['user_last_name']; ?>" class="user-thumb s25 inline-block mb10" style="background-image:url(<?php echo $user_list[$user_id]['user_image'] ? $user_list[$user_id]['user_image'] : base_url().'assets/images/default/user.png'; ?>);"></a><?php
                    }
                  } else { ?>
                    <p>Be the first to got this reward.</p>
                  <?php } ?>
                </li>
                <li class="box">
                  <p><span class="tc-green6 bold">Quanity: </span><?php echo $reward_item['redeem']['amount']?></p>
                  <p><span class="tc-green6 bold">Value: </span><?php echo $reward_item['value']?></p>
                  <p><span class="tc-green6 bold">Required point: </span><span class="point fs14"><?php echo $reward_item['redeem']['point']?></span></p>
                </li>
                <li><a href="" class="btn green w100 large"><span>View this reward</span></a></li>
              </ul>
            </div>
            <div class="section bd0 p15 mb10">
              <div class="tc-green6 fs16 bold"><?php echo $reward_item['name']?></div>
              <div class="description"><?php echo nl2br($reward_item['description']);?></div>
            </div>
          </div><?php 
        }
      } else { ?>
        <div class="no-item">No wishlist.</div><?php
      } ?>
      </div>
      <div class="pagination-reward strip"></div>
    </div>

    <div>
      <div class="tab-head slim activity">
        <p class="tab-name">Recent Activity</p>
        <span class="fr">
            <a class="tab active" data-filter="">All</a>
            <a class="tab" data-filter="app">Application</a>
            <a class="tab" data-filter="campaign">Campaign</a>
            <a class="tab" data-filter="me">Me</a>
        </span>
        <p class="fr p10">Display:</p>
      </div>

      <div class="list_resent-activity"></div>
      <div class="pagination-activity strip"></div>
    </div>
  </div>