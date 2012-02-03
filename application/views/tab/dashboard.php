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

    <div class="main-menu tab-white mb15">
      <div class="tab active" data-href="page-dashboard">Dashboard</div>
      <div class="tab" data-href="page-reward">Reward</div>
      <div class="tab" data-href="page-activities">Activities</div>
    </div>
	   
    <div class="main-content">
      <div class="campaign-box">
        <div class="tab-head slim">
          <p class="tab-name">Campaign</p>
          <span class="fr">
              <a class="tab active" data-filter="">All</a>
              <a class="tab" data-filter="active">Active</a>
              <a class="tab" data-filter="expired">Expired</a>
          </span>
          <p class="fr p10">Display:</p>
        </div>
        <div class="list-campaign"></div>
        <div class="pagination-campaign strip"></div>
      </div>

      <div class="applications-box">
        <div class="tab-head slim">
          <p class="tab-name">Applications</p>
          <a class="view-all-apps fr underline">View all</a>
        </div>
        <div class="app-icon-list<?php echo $apps ? '' : ' no-app'; ?>"><?php
            if($apps)
            {
              foreach($apps as $app)
              { ?> <div class="app-container">
                  <a class="app-icon" href="<?php echo '#'.$app['app_id']; ?>" title="<?php echo $app['app_name']; ?>" ><img class="app-image" width="64" height="64" src="<?php echo $app['app_image']; ?>" onerror="failsafeImg(this)" /></a>
                  <a class="app-name" href="<?php echo '#'.$app['app_id']; ?>" title="<?php echo $app['app_name']; ?>" ><?php echo $app['app_name']; ?></a>
                </div><?php
              }
            }
            else
            { ?>
              <li class="app-container">No application.</li><?php
            } ?>
        </div>
      </div>

      <div class="reward-box">
        <div class="tab-head slim">
          <p class="tab-name">Reward</p>
          <span class="fr">
              <a class="tab active" data-filter="">All</a>
              <a class="tab" data-filter="active">Active</a>
              <a class="tab" data-filter="expired">Expired</a>
          </span>
          <p class="fr p10">Display:</p>
        </div>

        <div class="list-reward p25"></div>
        <div class="pagination-reward strip mt5 mb5"></div>
      </div>
  	
      <div class="activity-box">
        <div class="tab-head slim">
          <p class="tab-name">Recent Activity</p>
          <span class="fr">
              <a class="tab active" data-filter="">All</a>
              <a class="tab" data-filter="app">Application</a>
              <a class="tab" data-filter="campaign">Campaign</a>
              <a class="tab" data-filter="me">Me</a>
          </span>
          <p class="fr p10">Display:</p>
        </div>

        <div class="list-recent-activity"></div>
        <div class="pagination-activity strip"></div>
      </div>
    </div>
  </div>