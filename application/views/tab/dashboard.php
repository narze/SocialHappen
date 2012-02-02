<div class="wrapper-content">	
  <div class="account-data">
      <div class="pic"><img src="<?php echo imgsize($page['page_image'],'normal');?>" alt="" /><span></span></div>
		<div class="data">
			<h1><?php echo $page['page_name'];?></h1>
			<?php if($is_logged_in && $is_admin) { ?>
			<ul class="viewas toggle">
				<li class="active"><a>View as</a><span class="arrow"></span></li>
				<li><a class="view-as-user">Member</a></li>
				<li class="last"><a class="view-as-guest">Guest</a></li>
			</ul>
			<ul class="publish toggle">
				<li class="active" id="published"><a>Published</a><span class="arrow"></span><span class="light"></span></li>
				<li id="unpublished"><a>Unpublished</a><span class="light"></span></li>
			</ul>
			<div class="buttons">
        <?php if(!$get_started_completed) { ?><a class="bt-get-started">Get started</a><?php } ?>
      </div>
			<?php } ?>
			<ul class="counter">
			  <li class="member" alt="Member"><a><?php echo issetor($page_user_count,'-');?></a></li>
			  <li class="activities" alt="Activities"><a><?php echo issetor($page_activities_count,'-');?></a></li>
			  <li class="applications" alt="Applications"><a><?php echo issetor($page_apps_count,'-');?></a></li>
			  <li class="campaigns" alt="Campaigns"><a><?php echo issetor($page_campaigns_count,'-');?></a></li>
			</ul>
		</div>
    </div>

    <div class="main-memu tab-white mb15">
      <div class="tab active">Dashboard</div>
      <div class="tab">Reward</div>
      <div class="tab">Activities</div>
    </div>
	
    <div>
      <div class="tab-head campaign">
        <h2>Campaign</h2>
        <div>
          <p>Display:</p>
          <ul>
            <li><a class="active" data-filter="">All</a></li>
            <li><a data-filter="active">Active</a></li>
            <li><a data-filter="expired">Expired</a></li>
          </ul>
        </div>
      </div>
      <div class="list_app-camp"></div>
      <div class="pagination-app-campaign strip"></div>
    </div>

    <div>
      <div class="tab-head">
        <h2>Applications</h2>
        <div>
          <a class="view-all apps">View all</a>
        </div>
      </div>
      <div class="app-icon-list<?php echo $apps ? '' : ' no-app'; ?>"><?php
          if($apps)
          {
            foreach($apps as $app)
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
      <div class="tab-head">
        <h2>Reward</h2>
        <div>
          <p>Display:</p>
          <ul>
            <li><a class="active a-app-campaign">All</a></li>
            <li><a class="a-app">Active</a></li>
            <li><a class="a-campaign">Expired</a></li>
          </ul>
        </div>
      </div>
      <div class="list_reward"></div>
      <div class="pagination-reward strip"></div>
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
            <li><a class="a-activity-me">Me</a></li>
          </ul>
        </div>
      </div>
      <div class="list_resent-activity"></div>
      <div class="pagination-activity strip"></div>
    </div>
	
  </div>