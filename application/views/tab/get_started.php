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
			<div class="buttons"><a class="bt-dashboard">Dashboard</a></div>
			<?php } ?>
			<ul class="counter">
			  <li class="member" alt="Member"><a><?php echo issetor($page_user_count,'-');?></a></li>
			  <li class="activities" alt="Activities"><a><?php echo issetor($page_activities_count,'-');?></a></li>
			  <li class="applications" alt="Applications"><a><?php echo issetor($page_apps_count,'-');?></a></li>
			  <li class="campaigns" alt="Campaigns"><a><?php echo issetor($page_campaigns_count,'-');?></a></li>
			</ul>
		</div>
    </div>
	
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
			<ul><?php 
			if(isset($checklist['config_page'])) {
				foreach($checklist['config_page'] as $list)
				{ ?>
					<li>
						<span class="<?php echo $list['status'] ? 'done' : 'todo'; ?>"></span><?php 
						if($list['status']) 
						{ ?>
							<del><?php echo $list['name']; ?></del><?php 
						} 
						else 
						{ ?>
							<a target="_top" href="<?php echo $list['link']; ?>"><?php echo $list['name']; ?></a><?php 
						} ?>
					</li><?php 
				} 
			}?>
			</ul>
			<hr />
		</div>
		<div class="steps">
			<h3>Install First Application To Your Page</h3>
			<div class="icon-help">?</div>
			<div class="tips"><div><span class="arrow top"></span><p>We design a platform that allows you to select what matches your business objectives. Each App has different purpose, so you can choose to install the one that fits. For example, Quiz App is for education and engagement purpose. Youtube App is for broadcasting your video channel on Facebook. Choose your first App from Application List.</p></div></div>
			<ul><?php 
			if(isset($checklist['install_app'])) {
				foreach($checklist['install_app'] as $list) 
				{ ?>
					<li>
						<span class="<?php echo $list['status'] ? 'done' : 'todo'; ?>"></span><?php 
						if($list['status']) 
						{ ?>
							<del><?php echo $list['name']; ?></del><?php 
						} 
						else 
						{ ?>
							<a target="_top" href="<?php echo $list['link']; ?>"><?php echo $list['name']; ?></a><?php 
						} ?>
					</li><?php 
				} 
			} ?>
			</ul>
			<hr />
		</div>
		<div class="steps">
			<h3>Take A Tour</h3>
			<div class="icon-help">?</div>
			<div class="tips"><div><span class="arrow top"></span><p>See what else you can do with SocialHappen.</p></div></div>
			<ul><?php 
			if(isset($checklist['tour'])) {
				foreach($checklist['tour'] as $list) 
				{ ?>
					<li>
						<span class="<?php echo $list['status'] ? 'done' : 'todo'; ?>"></span><?php 
						if($list['status']) 
						{ ?>
							<del><?php echo $list['name']; ?></del><?php 
						} 
						else 
						{ ?>
							<a target="_top" href="<?php echo $list['link']; ?>"><?php echo $list['name']; ?></a><?php 
						} ?>
					</li><?php 
				} 
			}?>
			</ul>
			<hr />
		</div>
		<div class="note">Note: The Facebook account you are using right now will be the administrator account that you can manage your applications, members, and campaigns.</div>
    </div>
	
  </div>