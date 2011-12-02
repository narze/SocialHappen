<div class="wrapper-content" id="app-setting-template">	
	<div class="account-data">
      <div class="pic"><img src="<?php echo imgsize($app['app_image'],'normal');?>" alt="" /></div>
		<div class="data">
			<h1><?php echo $app['app_name'];?></h1>
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
			<ul id="app-setting-menu">
				<li><a class="config" title="App config" href="<?php echo base_url().'app/config/'.$app['app_install_id']; ?>"></a></li>
				<li><a class="campaign" title="Campaign Setting" href="<?php echo base_url().'settings/campaign/'.$app['app_install_id']; ?>"></a></li>
				<li><a class="invite" title="Invite Setting"></a></li>
				<li><a class="share" title="Share Button Setting"></a></li>
				<li><a class="non-fan" title="Non-Fan Homepage Setting" href="<?php echo base_url().'settings/app_component/homepage/'.$app['app_install_id']; ?>"></a></li>
			</ul>
			<?php } ?>
		</div>
    </div>
	
	<div id="app-content">{APP_CONTENT}</div>
	
</div>