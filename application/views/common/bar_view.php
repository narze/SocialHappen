<div>
	<h1><span>SocialHappen</span></h1>	
	<?php if(isset($user)) : ?>
	<div class="goto">
        <p><a href="#">Go to</a></p>
		 <div>
          <ul>
          </ul>
          <p><a class="bt-create_company" href="#"><span>Create Company</span></a></p>
        </div>
	</div>
	<ul>
		<li class="name">
			<img src="<?php echo imgsize(issetor($user['user_image']),'square');?>" alt="" />
			<?php echo issetor($user['user_first_name']).' '.issetor($user['user_last_name']); ?>
			<ul>
				<li><?php echo anchor("settings?s=account&id={$user['user_id']}",'&raquo Profile Setting');?></li>
				<li><?php echo anchor('home/logout','&raquo Logout');?></li>
			</ul>
		</li>
	</ul>
	<?php else : ?>
	<ul>
		<li class="profile"><?php echo anchor('home/login','<span>Login</span>');?></a></li>
    </ul>
	<?php endif; ?>
</div>
