{header}
<div class="title-name">
	<h2><?php echo $title; ?></h2>
</div>
{breadcrumb}
<div class="wrapper-content">
	<div>
		<div class="wrapper-register">
			<?php if(issetor($from) == 'login') : ?>
				<div class="notice warning">You aren't SocialHappen member, please signup first.</div>
			<?php endif; ?>
			<div class="register-content">
				
				<div class="hello-user">
					<?php echo form_error('user_image'); ?>
					<div class="img-wrapper"><img src="<?php echo $user_profile_picture.'?type=normal';?>" /></div>
					<h3>Hello, <span style="color:#3b5998"><?php echo $facebook_user['first_name'].' '.$facebook_user['last_name'];?></span></h3>
					<?php if(!$is_registered) { ?><p><span style="color:#6e8b19">Sign up</span> in a few steps to power up your business on <span style="color:#3b5998">Facebook</span></p> <?php } ?>
				</div>
				
				<div class="slides">
					<div class="slide-wrapper">
						<div class="slide"><img src="../assets/images/regist_slide_01.jpg" alt="" /></div>
						<div class="slide"><img src="../assets/images/regist_slide_02.jpg" alt="" /></div>
						<div class="slide"><img src="../assets/images/regist_slide_03.jpg" alt="" /></div>
					</div>
					<div class="slide-ctrl">
						<ul>
							<li class="active"><a>1</a></li>
							<li><a>2</a></li>
							<li><a>3</a></li>
						</ul>
					</div>
				</div>

				<div class="form"><?php 
					if($is_registered) 
					{ ?>
						<h3 style="font-size:20px;margin:18px 0">You have already registered to SocialHappen</h3>
						<a href="<?php echo base_url().'?logged_in=true'; ?>" class="bt-go_dashboard">Go to Dashboard</a>
						<br /><?php 
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