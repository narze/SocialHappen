<div class="wrapper-content">	
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
			<div class="buttons"><a class="bt-get-started">Get Started</a></div>
			<?php } ?>
		</div>
    </div>
	
	<div id="get-started">
		<div class="tab-head">
        <h2><?php echo $app['app_name'];?> has been installed</h2>
		</div>
		<div class="info">
			<p>Yay! You've installed '<?php echo $app['app_name'];?>' to your page. You need a few more steps to configure this app before published to your page.</p>
			<p><a class="bt-configure app-config" href="<?php echo base_url(); ?>app/config/<?php echo $app['app_install_id']; ?>">Configure</a></p>
		</div>
		<div class="more-info">
			<h2>Learn more about '<?php echo $app['app_name'];?>' app</h2>
			<img src="<?php echo base_url(); ?>assets/images/imagedisplay-427-128.jpg" width="468" />
			<p>Nam hendrerit rutrum aliquam. Praesent aliquet turpis ac dui ornare a dictum est semper. Mauris luctus vulputate enim, at consectetur dui pellentesque at. Nullam malesuada justo vel mi vehicula vulputate viverra purus blandit. Sed sit amet nulla ac sem imperdiet molestie.</p>
			<p>Quisque condimentum justo at metus condimentum dictum. Mauris pulvinar sodales nunc, sit amet suscipit sem pretium at. Maecenas tristique metus metus. In posuere turpis ac nibh consectetur ullamcorper.</p>
			<p><a class="bt-learn-more">Learn more</a></p>
			<hr />
		</div>
		
		<div class="note">Note: The Facebook account you are using right now will be the administrator account that you can manage your applications, members, and campaigns.</div>
    </div>
	
  </div>