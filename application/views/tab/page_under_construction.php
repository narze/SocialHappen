<div class="wrapper-content">	
	<div class="account-data">
      <div class="pic"><img src="<?php echo imgsize(issetor($page['page_image']),'normal');?>" alt="" /><span></span></div>
		<div class="data">
			<h1><?php echo issetor($page['page_name'], 'Page not found');?></h1>
			<p><?php echo issetor($page['page_detail']);?></p>
		</div>
    </div>
	<div id="under-construction">
		<div class="info">
			<img src="<?php echo base_url(); ?>assets/images/icon_under_construction.png" alt="This page is under construction."/>
			<h1>This page is under construction.</h1>
			<p>Please Come back again later</p>
			<p class="note">If you are an admin of this page please <a onclick="sh_guest();">login here</a> to config your page</p>
		</div>
		<div class="more-info">
			<h2>What you can get from SocialHappen</h2>
			<img src="<?php echo base_url(); ?>assets/images/imagedisplay-427-128.jpg" width="468" height="140"/>
			<h3>Why do I have to join a campaign?</h3>
			<ul>
				<li>Tell users why they have to create a campaign on the page.</li>
				<li>Vestibulum ultricies arcu nec risus congue laoreet.</li>
				<li>Proin in justo sit amet purus viverra facilisis condimentum vitae magna.</li>
				<li>Ut aliquet varius magna, ac fringilla lectus placerat a.</li>
			</ul>
			<img src="<?php echo base_url(); ?>assets/images/imagedisplay-427-128.jpg" width="468" height="140"/>
			<h3>Every one love game</h3>
			<ul>
				<li>Aliquam non massa lectus, in viverra turpis.</li>
				<li>Morbi commodo malesuada massa, vel vulputate purus scelerisque eget.</li>
			</ul>
		</div>
	
	</div>
	
  </div>