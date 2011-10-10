{header}
{breadcrumb}
<div class="wrapper-content">
	<div>
		<div class="wrapper-register">
			<?php if(issetor($from) == 'login') : ?>
				<div class="notice warning">You aren't SocialHappen member, please signup first.</div>
			<?php endif; ?>
			<div class="register-content">
				<div>
					<h2>Tutorial</h2>
					<div><img src="../assets/images/banner-slider.png" alt="Tutorial" /></div>
				</div>
				<div class="form"><?php 
					if($is_registered) 
					{ ?>
						<h1 style="font-size:20px;margin:18px 0">You have already registered to SocialHappen</h1>
						<a href="<?php echo base_url().'?logged_in=true'; ?>" class="bt-go_dashboard">Go to Dashboard</a><?php 
					} 
					else 
					{ ?>
						{signup_form}<?php 
					} ?>
				</div>
			</div>
		</div>
	</div>
	<div class="bottom"><!--bottom--></div>
</div>
{footer}