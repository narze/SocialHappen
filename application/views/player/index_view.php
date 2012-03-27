{header}
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div><?php
					if($user) {
						echo anchor('player/challenge_list', 'View ALL Challenges', 'class="btn btn-primary"').' ';
						echo anchor('player/challenging_list', 'View Challenging Challenges', 'class="btn btn-primary"').' ';
						echo anchor('player/settings', 'Player settings', 'class="btn"').' ';
					} else {
						echo anchor('player/login', 'Login', 'class="btn btn-primary"').' or ';
						echo anchor('player/signup', 'Signup Socialhappen').'<br/>';
						
					} ?>

					<div>
					<?php if($facebook_connected) : ?>
						You are connected to facebook
					<?php else : ?>
						You are not connected to facebook
					<?php endif; ?>
					</div>
				</div>
				<div class="bottom"><!--bottom--></div>
			</div>
		</div>
	</div>
