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
			<div class="buttons"><a class="bt-get-started">Get started</a></div>
			<?php } ?>
			<ul class="counter">
			  <li class="member"><a><?php echo issetor($page_user_count,'-');?></a></li>
			  <li class="activities"><a><?php echo issetor($page_activities_count,'-');?></a></li>
			  <li class="applications"><a><?php echo issetor($page_apps_count,'-');?></a></li>
			  <li class="campaigns"><a><?php echo issetor($page_campaigns_count,'-');?></a></li>
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
            <li><a class="a-activity-me">Me</a></li>
          </ul>
        </div>
      </div>
      <div class="list_resent-activity"></div>
      <div class="pagination-activity strip"></div>
    </div>
	
  </div>