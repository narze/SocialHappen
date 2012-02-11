<div class="wrapper-content">
		<div class="account-data">
			<div class="pic"><img src="<?php echo $user['user_image'].'?type=normal';?>" /><span></span></div>
			<div class="data">
				<h1><?php echo $user['user_first_name']." ".$user['user_last_name'];?></h1>
				<div class="my-point abs-r fs16"><?php echo number_format($page_score);?></div>
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
				<li><a><img class="image" src="<?php echo $friend['image']; ?>" title="<?php echo $friend['name'];?>" /></a></li><?php 
			endforeach; 
			endif;?>
				</ul>
				<p><a class="link-friendsjoin">1,220 friends joined this page</a></p>
				<p><a class="link-invite_friend">invite friends</a></p>
			</div>
*/ ?>

			

		</div><!-- end account-data-->

		<div class="main-menu tab-white">
			<div class="tab active" data-href="user-dashboard">Dashboard</div>
			<div class="tab" data-href="user-badges">My Badges<?php //echo ' ('.$user['total_achieved_badges'].')' ?></div>
			<div class="tab" data-href="user-activities">Activities</div>
		</div>
		 
		<div class="main-content">

			<div class="campaign-box mt15">
				<div class="tab-head slim">
					<p class="tab-name">My Campaign</p>
					<span class="fr">
							<a class="tab active" data-filter="me">All</a>
							<a class="tab" data-filter="me-active">Active</a>
							<a class="tab" data-filter="me-expired">Expired</a>
					</span>
					<p class="fr p10">Display:</p>
				</div>
				<div class="list_app-camp"></div>
				<div class="pagination-app-campaign strip"></div>
			</div>

			<div class="applications-box">
				<div class="tab-head slim">
					<p class="tab-name">My Applications</p>
					<a class="view-all-apps fr underline">View all</a>
				</div>
				<div class="app-icon-list<?php echo $user_apps ? '' : ' no-app'; ?>"><?php
						if($user_apps)
						{
							foreach($user_apps as $app)
							{ ?> <div class="app-container">
									<a class="app-icon" target="_top" href="<?php echo $app['facebook_tab_url']; ?>" title="<?php echo $app['app_name']; ?>" ><img class="app-image" width="64" height="64" src="<?php echo $app['app_image']; ?>" onerror="failsafeImg(this)" /></a>
									<a class="app-name" target="_top" href="<?php echo $app['facebook_tab_url']; ?>" title="<?php echo $app['app_name']; ?>" ><?php echo $app['app_name']; ?></a>
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
					<p class="tab-name">Wishlist</p>
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
							<a class="tab" data-filter="">All</a>
							<a class="tab" data-filter="me_app">Application</a>
							<a class="tab" data-filter="me_campaign">Campaign</a>
							<a class="tab active" data-filter="me">Me</a>
					</span>
					<p class="fr p10">Display:</p>
				</div>

				<div class="list-recent-activity"></div>
				<div class="pagination-activity strip"></div>
			</div>

		</div>
	</div>