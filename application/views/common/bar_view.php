<div>
	<ul>

        <li class="profile"><a href="#"><span></span></a></li>
        <li class="name"><img src="<?php echo issetor($user['user_image']);?>" alt="" />
			<?php if(isset($user)) {
				echo issetor($user['user_first_name']).' '.issetor($user['user_last_name']); 
			} else {
				echo 'Login';
			}?></li>

        <li class="drop">
			<a href="#">
				<span>
					<?php if(isset($user) && isset($company_id)) : ?>
						<div id="user-menu">
							<div id="profile-setting"><?php echo anchor("settings/{$company_id}/account/{$user['user_id']}",'Profile Setting');?></div>
							<div id="logout"><?php echo anchor('home/logout','Logout');?></div>
						</div>
					<?php endif; ?>
				</span>
			</a>
		</li>

      </ul>
</div>
<div id="goto"><h2><a href="#">Go to</a></h2><div id="goto-list"></div></div>
