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
	
	<?php if($get_started) { ?>
	<div id="get-started">
		<div class="tab-head">
        <h2>Get Started !</h2>
		</div>
		<div class="info">
			<p>Hello Admin! You are a few minutes away from having a powerful social membership system that integrate gaming mechanic right onto your Facebook page ...</p>
			<p>You have installed SocialHappen to your Facebook Page. Now you can configure the system.</p>
			<p style="text-align:right"><a>Learn more</a></p>
		</div>
		<div class="steps">
			<h3>Configure Member System</h3>
			<div class="icon-help">?</div>
			<div class="tips"><div><span class="arrow top"></span><p>SocialHappen alllows you to have your own in-page Membership System which you can access all your members' profiles in one place. Every Apps you installed from SocialHappen share the same registration system. Configure your own sign up form as you wish !</p></div></div>
			<ul>
				<li><a>Configure Your Own Sign-Up Form</a></li>
				<li><a>View How Your Members See The Sign-Up Form</a></li>
			</ul>
			<hr />
		</div>
		<div class="steps">
			<h3>Install First Application To Your Page</h3>
			<div class="icon-help">?</div>
			<div class="tips"><div><span class="arrow top"></span><p>We design a platform that allows you to select what matches your business objectives. Each App has different purpose, so you can choose to install the one that fits. For example, Quiz App is for education and engagement purpose. Youtube App is for broadcasting your video channel on Facebook. Choose your first App from Application List.</p></div></div>
			<ul>
				<li><a>Go To Application List</a></li>
				<li><a>See Where I Can Manage My Applications</a></li>
			</ul>
			<hr />
		</div>
		<div class="steps">
			<h3>Take A Tour</h3>
			<div class="icon-help">?</div>
			<div class="tips"><div><span class="arrow top"></span><p>See what else you can do with SocialHappen.</p></div></div>
			<ul>
				<li><del>Learn How to Manage Your Page and Applications</del></li>
				<li><a>Learn How Your Members See SocialHappen Tab</a></li>
				<li><a>Learn How Your Members Interact With Your Page</a></li>
				<li><a>Learn How to View Members' Profiles and Their Activities</a></li>
				<li><a>Learn How to Manage Campaign</a></li>
			</ul>
			<hr />
		</div>
		<div class="note">Note: The Facebook account you are using right now will be the administrator account that you can manage your applications, members, and campaigns.</div>
    </div>
	<?php } else {?>
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
	<?php } ?>
	
  </div>