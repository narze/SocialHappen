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
	<?php endif; ?>
	<ul>
        <li class="profile"><a href="#"><span></span></a></li>
        <li class="name">
			<?php if(isset($user)) {
				echo '<img src="'.issetor($user['user_image']).'" alt="" />'.issetor($user['user_first_name']).' '.issetor($user['user_last_name']); 
				echo '<ul>
						<li>'.anchor("settings?s=account&id={$user['user_id']}",'&raquo Profile Setting').'</li>
						<li>'.anchor('home/logout','&raquo Logout').'</li>
					  </ul>';
			} else {
				echo anchor('home/login','Login');
			}?>
		</li>
    </ul>
</div>
