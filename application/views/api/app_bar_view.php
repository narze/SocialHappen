<div class="header">
	<div class="name">
		<div>
			<p class="pic"><img src="<?php echo $app_icon_url; ?>" alt="" /><span></span>
			</p>
			<p>
				<?php echo $app_name; ?>
			</p>
		</div>
		<ul>
			<?php
				foreach($left_menu as $item){
					echo '<li><a href="'.$item['location'].'">'.$item['title'].'</a></li>';
				}
			?>
		</ul>
	</div>
	<ul class="menu">
		<!--
		<li class="like">
			<a href="#"><span>like</span></a>
		</li>
		<li class="message">
			<a href="#"><span>10</span></a>
		</li>
		-->
		<li class="profile">
			<div>
				<?php if(in_array($view_as, array('admin','user'))) : ?>
					<p class="pic"><img src="<?php echo $user_diplay_picture_url; ?>" alt="<?php echo $user_display_name; ?>" />
					</p>
					<p>
						<?php echo $user_display_name; ?>
					</p>
				<?php elseif($view_as == 'guest') : ?>
					
					<p>
						Guest
					</p>
				<?php endif; ?>
			</div>
			<ul>
				<?php
					foreach($right_menu as $item){
						echo '<li><a href="'.$item['location'].'">'.$item['title'].'</a></li>';
					}
				?>
			</ul>
		</li>
		<?php if($view_as == 'admin') : ?>
			<li class="setting">
				<a href="#"><span>setting</span></a>
			</li>
		<?php endif; ?>
	</ul>
</div>
