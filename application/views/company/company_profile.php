<div class="box-information">
	<div class="details">
	  <div class="pic">
	    <p><img src="<?php echo imgsize(issetor($company_profile['company_image']),'large'); ?>" alt=""></p>
	    <p><a class="bt-go_page" href="#"><span>Goto Page</span></a></p>
	    <p><a class="bt-add_app" href="#"><span>Add App</span></a></p>
	  </div>
	  <h2><?php echo issetor($company_profile['company_name']); ?></h2>
	  <p><?php echo issetor($company_profile['company_detail']); ?></p>
	</div>
	<div class="information">
	  <h2>Information</h2>
	  <ul>
	    <li id="info-installed-page"><span>Installed Page</span>0</li>
	    <li id="info-installed-app"><span>Installed Application</span>0</li>
	  </ul>
	</div>
</div>